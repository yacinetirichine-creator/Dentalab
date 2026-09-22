<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Lang;
use PHPUnit\Framework\Attributes\DataProvider;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * Règle n° 7 du projet : aucune chaîne d'interface écrite en dur.
 *
 * Ce test relit les composants React et échoue si du texte destiné à
 * l'utilisateur y apparaît directement, au lieu de passer par
 * lang/<langue>/interface.php et le crochet useTraduction().
 *
 * C'est un garde-fou, pas un analyseur syntaxique : il attrape les trois
 * formes qui reviennent en pratique — le texte entre deux balises, les
 * attributs visibles, et les caractères accentués, qui trahissent du
 * français oublié dans le code.
 */
class InterfaceTraduiteTest extends TestCase
{
    /** Noms propres, qui ne se traduisent pas. */
    protected const TOLERES = ['Dentalab'];

    /** Attributs dont la valeur s'affiche à l'écran. */
    protected const ATTRIBUTS_VISIBLES = ['title', 'placeholder', 'alt', 'aria-label', 'label'];

    /**
     * @return array<string, array{string}>
     */
    public static function composants(): array
    {
        $fichiers = Finder::create()
            ->files()
            ->in(dirname(__DIR__, 2).'/resources/js')
            ->name('*.tsx');

        $cas = [];

        foreach ($fichiers as $fichier) {
            /** @var SplFileInfo $fichier */
            $cas[$fichier->getRelativePathname()] = [$fichier->getRealPath()];
        }

        return $cas;
    }

    #[DataProvider('composants')]
    public function test_un_composant_ne_contient_aucune_chaine_d_interface(string $chemin): void
    {
        $this->assertSame(
            [],
            self::chainesEnDurDans($chemin),
            'Ces textes appartiennent à lang/fr/interface.php, et se lisent avec useTraduction().'
        );
    }

    public function test_le_garde_fou_attrape_bien_une_chaine_oubliee(): void
    {
        $piege = tempnam(sys_get_temp_dir(), 'composant').'.tsx';

        file_put_contents($piege, <<<'TSX'
            export default function Piege() {
                return <p>Bonjour tout le monde</p>;
            }
            TSX);

        try {
            $this->assertSame(
                ['texte affiché : « Bonjour tout le monde »'],
                self::chainesEnDurDans($piege)
            );
        } finally {
            @unlink($piege);
        }
    }

    public function test_le_garde_fou_attrape_aussi_un_attribut_visible(): void
    {
        $piege = tempnam(sys_get_temp_dir(), 'composant').'.tsx';

        file_put_contents($piege, <<<'TSX'
            export default function Piege() {
                return <input placeholder="Votre adresse" />;
            }
            TSX);

        try {
            $this->assertSame(
                ['attribut placeholder="Votre adresse"'],
                self::chainesEnDurDans($piege)
            );
        } finally {
            @unlink($piege);
        }
    }

    public function test_toutes_les_cles_utilisees_existent_bien(): void
    {
        $manquantes = [];

        foreach (self::composants() as [$chemin]) {
            preg_match_all("/\\bt\\('([^']+)'/", (string) file_get_contents($chemin), $cles);

            foreach ($cles[1] as $cle) {
                if (! Lang::has('interface.'.$cle)) {
                    $manquantes[] = basename($chemin).' → '.$cle;
                }
            }
        }

        $this->assertSame(
            [],
            $manquantes,
            'Ces clés sont appelées par un composant mais absentes de lang/fr/interface.php.'
        );
    }

    /**
     * @return list<string> les chaînes d'interface trouvées en dur
     */
    protected static function chainesEnDurDans(string $chemin): array
    {
        $code = self::sansCommentaires((string) file_get_contents($chemin));
        $problemes = [];

        // 1. Du texte posé entre deux balises : <p>Bonjour</p>
        preg_match_all('/>([^<>{}]*\p{L}{2,}[^<>{}]*)<\//u', $code, $textes);

        foreach ($textes[1] as $texte) {
            $texte = trim($texte);

            if ($texte !== '' && ! in_array($texte, self::TOLERES, strict: true)) {
                $problemes[] = "texte affiché : « {$texte} »";
            }
        }

        // 2. Un attribut visible renseigné avec une chaîne littérale
        $attributs = implode('|', self::ATTRIBUTS_VISIBLES);
        preg_match_all('/\b('.$attributs.')="([^"]*\p{L}{2,}[^"]*)"/u', $code, $valeurs, PREG_SET_ORDER);

        foreach ($valeurs as [, $attribut, $valeur]) {
            if (! in_array($valeur, self::TOLERES, strict: true)) {
                $problemes[] = "attribut {$attribut}=\"{$valeur}\"";
            }
        }

        // 3. Un caractère accentué hors commentaire : du français oublié.
        if (preg_match('/[À-ÖØ-öø-ÿ]/u', $code, $trouve)) {
            $problemes[] = 'texte accentué hors commentaire : « '.self::ligneContenant($code, $trouve[0]).' »';
        }

        return $problemes;
    }

    /** Retire les commentaires de bloc et les lignes entièrement commentées. */
    protected static function sansCommentaires(string $code): string
    {
        $code = (string) preg_replace('#/\*.*?\*/#s', '', $code);

        return (string) preg_replace('#^\s*//.*$#m', '', $code);
    }

    protected static function ligneContenant(string $code, string $aiguille): string
    {
        foreach (explode("\n", $code) as $ligne) {
            if (str_contains($ligne, $aiguille)) {
                return trim($ligne);
            }
        }

        return $aiguille;
    }
}
