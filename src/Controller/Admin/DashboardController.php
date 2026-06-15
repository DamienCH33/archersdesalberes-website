<?php

namespace App\Controller\Admin;

use App\Repository\AlbumRepository;
use App\Repository\ArticleRepository;
use App\Repository\PartnerRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private PartnerRepository $partnerRepository,
        private PhotoRepository $photoRepository,
        private AlbumRepository $albumRepository,
        private UserRepository $userRepository
    ) {}

    public function index(): Response
    {
        $conn = $this->userRepository->getEntityManager()->getConnection();

        $adminsCount = $conn->fetchOne("
            SELECT COUNT(*)
            FROM \"user\"
            WHERE roles::text LIKE '%ROLE_ADMIN%'
        ");

        $stats = [
            'articles' => $this->articleRepository->count([]),
            'partners' => $this->partnerRepository->count([]),
            'photos'   => $this->photoRepository->count([]),
            'albums'   => $this->albumRepository->count([]),
            'admins'   => (int) $adminsCount,
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/logoarcherie-transparent.png" style="width: 40px; height: 40px; object-fit: contain;">')
            ->setFaviconPath('images/logoarcherie-transparent.png')
            ->setTranslationDomain('admin')
            ->setDefaultColorScheme('dark')
            ->disableDarkMode();
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->displayUserName(false)
            ->displayUserAvatar(false);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Contenu');
        yield MenuItem::linkToRoute('Actualités', 'fa fa-newspaper', 'admin_article_index');
        yield MenuItem::subMenu('Galerie photos', 'fa fa-images')->setSubItems([
            MenuItem::linkToRoute('Albums', 'fa fa-folder', 'admin_album_index'),
            MenuItem::linkToRoute('Photos', 'fa fa-image', 'admin_photo_index'),
        ]);

        yield MenuItem::section('Le Club');
        yield MenuItem::linkToRoute('Chiffres clés', 'fa fa-chart-line', 'admin_club_stat_index');
        yield MenuItem::linkToRoute('Équipe', 'fa fa-users', 'admin_team_member_index');
        yield MenuItem::linkToRoute('Partenaires', 'fa fa-handshake', 'admin_partner_index');

        yield MenuItem::section('Administration');
        yield MenuItem::linkToRoute('Administrateurs', 'fa fa-user-shield', 'admin_user_index');
        yield MenuItem::linkToRoute('Paramètres', 'fa fa-cog', 'admin_setting_index');

        yield MenuItem::section();
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-eye', 'app_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
