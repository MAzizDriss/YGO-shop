<?php

namespace App\DataFixtures;

use App\Entity\Deck;
use App\Repository\DeckRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Card;

class AppFixtures extends Fixture
{
    /**
     * Generates initialization data for decks : [owner] //V0
     * @return \\Generator
     */
    private static function decksDataGenerator()
    {
        yield ["Aziz Driss"];
        yield ["Kaiba"];
        yield ["Yu-Gi"];
    }

    /**
     * Generates initialization data for film recommendations:
     *  [card_name, card_class, deck]
     * @return \\Generator
     */
    private static function CardsGenerator()
    {
        yield ["Blue-Eyes White Dragon", "Dragon / Normal", "Aziz Driss"];
        yield ["Cyber Eternity Dragon", "Machine / Fusion / Effect", "Aziz Driss"];
        yield ["Cyberload Fusion", "Spell", "Aziz Driss"];
        yield ["Continuous Trap", "Trap_card", "Kaiba"];
        yield ["Beast King Barbaros", "Beast-Warrior / Effect", "Kaiba"];
        yield ["Breaker the Magical Warrior", "Spellcaster / Effect", "Kaiba"];
        yield ["Stardust Synchron", "Machine / Tuner / Effect", "Yu-Gi"];
        yield ["Saambell the Star Bonder", "Spellcaster / Pendulum / Effect", "Yu-Gi"];
        yield ["Despian Comedy", "Fairy / Effect", "Yu-Gi"];
        yield ["Stardust Trail", "Dragon / Effect", "Yu-Gi"];  
    }

    public function load(ObjectManager $manager)
    {
        $deckRepo = $manager->getRepository(Deck::class);

        foreach (self::decksDataGenerator() as [$owner] ) {
            $deck = new Deck();
            $deck->setOwner($owner);
            $manager->persist($deck);          
        }
        $manager->flush();

        foreach (self::CardsGenerator() as [$card_name, $card_class, $deck_owner])
        {
            $deck = $deckRepo->findOneBy(['owner' => $deck_owner]);
            $card = new Card();
            $card->setCardName($card_name);
            $card->setCardClass($card_class);
            $deck->addCard($card);
            // there's a cascade persist on deck-cards which avoids persisting down the relation
            $manager->persist($card);
        }
        $manager->flush();
    }
}

