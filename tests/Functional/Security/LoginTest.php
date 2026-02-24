<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    private function createClientAndGoToLogin(): array
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful('La page de login doit retourner 200');
        $this->assertSelectorTextContains('h2', 'Connexion à Review Sym');

        return [$client, $crawler];
    }

    public function testLoginPageIsAccessible(): void
    {
        [$client, $crawler] = $this->createClientAndGoToLogin();

        $this->assertSelectorExists('input[name="username"]');
        $this->assertSelectorExists('input[name="password"]');
        $this->assertSelectorExists('button[type="submit"]');
        // $this->assertSelectorExists('input[name="_remember_me"]'); // si tu l'utilises
    }

    public function testLoginWithBadCredentialsShowsError(): void
    {
        [$client, $crawler] = $this->createClientAndGoToLogin();

        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'admintest',
            'password' => 'wrong-password',
        ]);

        $client->submit($form);

        // Symfony redirige sur la même page en cas d'erreur
        $this->assertResponseRedirects('/login');
        $client->followRedirect();

        $this->assertSelectorExists('.bg-red-50');
        $this->assertSelectorTextContains(
            '.bg-red-50',
            'Invalid credentials.',
            'Le message d\'erreur "Invalid credentials" doit apparaître'
        );
    }

    /**
     * @dataProvider provideValidUsers
     */
    public function testSuccessfulLoginForEachRole(string $username, string $password, string $expectedRouteAfterLogin): void
    {
        $client = static::createClient();

        // Récupérer l'utilisateur depuis la base de test (doit exister grâce aux fixtures)
        $container = static::getContainer();
        $user = $container->get('doctrine')->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        $this->assertNotNull($user, "L'utilisateur '$username' doit exister dans la base de test");

        // Connexion directe via loginUser()
        $client->loginUser($user);

        // Vérifier qu'on est bien connecté
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('.bg-green-50');
        $this->assertSelectorTextContains('.bg-green-50', 'Vous êtes connecté en tant que');
        $this->assertSelectorTextContains('.bg-green-50', $username);

        // Vérifier la présence du lien de déconnexion
        $this->assertSelectorExists('a[href*="/logout"]');

        // Le formulaire de login ne doit plus être visible
        $this->assertSelectorNotExists('input[name="password"]');

        // Optionnel : vérifier la route après login réussi
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        // $this->assertRouteSame($expectedRouteAfterLogin); // décommente si tu veux tester la route exacte
    }

    public static function provideValidUsers(): array
    {
        return [
            'User simple'   => ['usertest',       'password', 'app_home'],
            'Admin'         => ['admintest',      'password', 'admin_dashboard'],
            'Superadmin'    => ['superadmintest', 'password', 'admin_dashboard'],
        ];
    }
}
