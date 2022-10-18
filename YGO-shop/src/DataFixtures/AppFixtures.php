<?php

namespace App\DataFixtures;

use App\Entity\Deck;
use App\Repository\DeckRepository;
use App\Repository\MemberRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Card;
use App\Entity\Member;


class AppFixtures extends Fixture
{

    /**
     * Generates initialization data for members : [name, description] //V0
     * @return \\Generator
     */

    private static function membersDataGenerator()
    {
        yield ["Aziz","eh"];
        yield ["Kaiba","no"];
        yield ["Yugi", "yey"];
    }

    /**
     * Generates initialization data for decks : [deck_name,owner] //V0
     * @return \\Generator
     */

    private static function decksDataGenerator()
    {
        yield ["Aziz's Deck","Aziz"];
        yield ["Kaiba's Deck","Kaiba"];
        yield ["Yugi's Deck","Yugi"];
    }

    /**
     * Generates initialization data for Deck cards:
     *  [card_name, card_class, deck_name]
     * @return \\Generator
     */
    private static function CardsGenerator()
    {
        yield ["Blue-Eyes White Dragon", "Dragon / Normal", "Aziz's Deck"];
        yield ["Cyber Eternity Dragon", "Machine / Fusion / Effect", "Aziz's Deck"];
        yield ["Cyberload Fusion", "Spell", "Aziz's Deck"];
        yield ["Continuous Trap", "Trap_card", "Kaiba's Deck"];
        yield ["Beast King Barbaros", "Beast-Warrior / Effect", "Kaiba's Deck"];
        yield ["Breaker the Magical Warrior", "Spellcaster / Effect", "Kaiba's Deck"];
        yield ["Stardust Synchron", "Machine / Tuner / Effect", "Yugi's Deck"];
        yield ["Saambell the Star Bonder", "Spellcaster / Pendulum / Effect", "Yugi's Deck"];
        yield ["Despian Comedy", "Fairy / Effect", "Yugi's Deck"];
        yield ["Stardust Trail", "Dragon / Effect", "Yugi's Deck"];  
    }

    public function load(ObjectManager $manager)
    {
        $memberRepo = $manager->getRepository(Member::class);
        $deckRepo = $manager->getRepository(Deck::class);
        
        foreach (self::membersDataGenerator() as [$name, $description] ) {

            $member = new Member();
            $member->setName($name);
            $member->setDescription($description);
            $manager->persist($member);          
        }
        $manager->flush();

        foreach (self::decksDataGenerator() as [$d_name, $owner_name] ) {

            $member = $memberRepo->findOneBy(['name' => $owner_name]);
            $deck = new Deck();
            $deck->setDName($d_name);
            $member->addDeck($deck);
            $manager->persist($deck);   

        }
        $manager->flush();

        foreach (self::CardsGenerator() as [$card_name, $card_class, $deck_name])
        {
            $deck = $deckRepo->findOneBy(['d_name' => $deck_name]);

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

