<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Repository\AssetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/assets')]
#[IsGranted('ROLE_USER')]
class AssetController extends AbstractController
{
    #[Route('/', name: 'app_assets')]
    public function index(AssetRepository $assetRepository): Response
    {
        $assets = $assetRepository->findAll();
        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
        ]);
    }

    #[Route('/new', name: 'app_asset_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AssetRepository $assetRepository): Response
    {
        $asset = new Asset();
        if ($request->isMethod('POST')) {
            $asset->setHostname($request->request->get('hostname'));
            $asset->setIp($request->request->get('ip'));
            $asset->setType($request->request->get('type'));
            $asset->setStatus($request->request->get('status', 'active'));
            
            $assetRepository->save($asset, true);
            return $this->redirectToRoute('app_assets');
        }

        return $this->render('asset/new.html.twig', [
            'asset' => $asset,
        ]);
    }

    #[Route('/{id}', name: 'app_asset_show')]
    public function show(Asset $asset): Response
    {
        return $this->render('asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_asset_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Asset $asset, AssetRepository $assetRepository): Response
    {
        if ($request->isMethod('POST')) {
            $asset->setHostname($request->request->get('hostname'));
            $asset->setIp($request->request->get('ip'));
            $asset->setType($request->request->get('type'));
            $asset->setStatus($request->request->get('status'));
            
            $assetRepository->save($asset, true);
            return $this->redirectToRoute('app_assets');
        }

        return $this->render('asset/edit.html.twig', [
            'asset' => $asset,
        ]);
    }

    #[Route('/{id}', name: 'app_asset_delete', methods: ['POST'])]
    public function delete(Request $request, Asset $asset, AssetRepository $assetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$asset->getId(), $request->request->get('_token'))) {
            $assetRepository->remove($asset, true);
        }

        return $this->redirectToRoute('app_assets');
    }
}
