<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocorico\CoreBundle\Controller\Dashboard\Offerer;

use Cocorico\CoreBundle\Entity\Listing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * ListingImage controller.
 *
 * @Route("/listing")
 */
class ListingImageController extends Controller
{

    /**
     * Edit Listing images entities.
     *
     * @Route("/{id}/edit_images", name="cocorico_dashboard_listing_edit_images", requirements={"id" = "\d+"})
     * @Security("is_granted('edit', listing)")
     * @ParamConverter("listing", class="CocoricoCoreBundle:Listing")
     *
     * @Method({"GET", "PUT", "POST"})
     *
     * @param Request $request
     * @param         $listing
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editImagesAction(Request $request, Listing $listing)
    {
        $editForm = $this->createEditImagesForm($listing);
        $editForm->handleRequest($request);

        $selfUrl = $this->generateUrl(
            'cocorico_dashboard_listing_edit_images',
            array(
                'id' => $listing->getId()
            )
        );

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->get("cocorico.listing.manager")->save($listing);

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('listing.edit.success', array(), 'cocorico_listing')
            );

            return $this->redirect($selfUrl);
        }

        $this->get('cocorico.breadcrumbs_manager')->addListingItem($request, $listing);
        $text = $this->get('translator')->trans('photos', array(), 'cocorico_breadcrumbs');
        $this->get('cocorico.breadcrumbs_manager')->addItem($text, $selfUrl);

        return $this->render(
            'CocoricoCoreBundle:Dashboard/Listing:edit_images.html.twig',
            array(
                'listing' => $listing,
                'form' => $editForm->createView()
            )
        );

    }

    /**
     * Creates a form to edit a Listing entity.
     *
     * @param Listing $listing The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditImagesForm(Listing $listing)
    {
        $form = $this->get('form.factory')->createNamed(
            'listing',
            'listing_edit_images',
            $listing,
            array(
                'action' => $this->generateUrl(
                    'cocorico_dashboard_listing_edit_images',
                    array('id' => $listing->getId())
                ),
                'method' => 'POST',
            )
        );

        return $form;
    }


}
