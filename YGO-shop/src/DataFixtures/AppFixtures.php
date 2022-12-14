<?php

namespace App\DataFixtures;

use App\Entity\Deck;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Card;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Member;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class AppFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * Generates initialization data for members : [name, description] //V0
     * @return \\Generator
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    private static function membersDataGenerator()
    {
        yield ["Aziz Driss","A TSP student and a beginner at YGO cards, Full of amibition and enthusiasm this young man
        is taking an advanture to learn about the Dueling world!","aziz@localhost"];
        yield ["Seto Kaiba","As the majority shareholder and CEO of his own multi-national gaming company, Kaiba Corporation,
         Kaiba is reputed to be Japan's greatest gamer and aims to become the world's
          greatest player of the American card game, Duel Monsters.","kaiba@localhost"];
        yield ["Yugi Mutou", "Yugi is introduced as a teenager who is solving an ancient Egyptian puzzle known as the Millennium Puzzle, 
        hoping it will grant him his wish of forming bonds","yugi@localhost"];
    }

    /**
     * Generates initialization data for decks : [deck_name,owner] //V0
     * @return \\Generator
     */

    private static function decksDataGenerator()
    {
        yield ["Creator's privilege","Aziz Driss"];
        yield ["Off-Dragon Deck","Seto Kaiba"];
        yield ["Heart of Cards","Yugi Mutou"];
    }

    /**
     * Generates initialization data for cards categories:
     *  [label, description, parent]
     * @return \\Generator
     */
    private static function ParentsCategoriesGenerator()
    {
        yield ["Spell","This type of cards have special effects that implies into the game","None"];
        yield ["Trap_card", "This type of cards can be placed faced down on the board and can be activated later","None"];
        yield ["Monster", "This type of cards can be placed in attack or defense mode","None"];
    }

    /**
     * Generates initialization data for cards categories:
     *  [label, description, parent]
     * @return \\Generator
     */
    private static function CategoriesGenerator()
    {

        yield ["Dragon", "The monster is a dragon", "Monster"];
        yield ["Beast-Warrior", "The monster is a Beast-warrior", "Monster"];
        yield ["Spellcaster", "A monster that can cast spells", "Monster"];
        yield ["Machine", "The monster is a Machine", "Monster"];
        yield ["Fairy", "The monster is a fairy ", "Monster"];
        yield ["Effect", "This monster has a special effect", "Monster"];  
        yield ["Fusion", "This monster can be fusioned", "Monster"];  
    }


    /**
     * Generates initialization data for Deck cards:
     *  [card_name, card_class, deck_name]
     * @return \\Generator
     */
    private static function CardsGenerator()
    {
        yield ["Blue-Eyes White Dragon", "Dragon", "Creator's privilege"];
        yield ["Cyber Eternity Dragon", "Machine Fusion Effect", "Creator's privilege"];
        yield ["Cyberload Fusion", "Spell", "Creator's privilege"];
        yield ["Continuous Trap", "Trap_card", "Off-Dragon Deck"];
        yield ["Beast King Barbaros", "Beast-Warrior Effect", "Off-Dragon Deck"];
        yield ["Breaker the Magical Warrior", "Spellcaster Effect", "Off-Dragon Deck"];
        yield ["Stardust Synchron", "Machine Effect", "Heart of Cards"];
        yield ["Saambell the Star Bonder", "Spellcaster Effect", "Heart of Cards"];
        yield ["Despian Comedy", "Effect", "Heart of Cards"];
        yield ["Stardust Trail", "Dragon Effect", "Heart of Cards"];  
    }

    public function load(ObjectManager $manager)
    {
        $memberRepo = $manager->getRepository(Member::class);
        $deckRepo = $manager->getRepository(Deck::class);
        $CatRepo = $manager->getRepository(Category::class);
        
        foreach (self::membersDataGenerator() as [$name, $description, $useremail] ) {

            $member = new Member();
            if ($useremail) {
                $user = $manager->getRepository(User::class)->findOneByEmail($useremail);
                $member->setUser($user);
            }
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

        foreach (self::ParentsCategoriesGenerator() as [$label, $description, $parent_label])
        {

            $category = new Category();
            $category->setLabel($label);
            $category->setDescription($description);

            $manager->persist($category);
        }
        $manager->flush();

        foreach (self::CategoriesGenerator() as [$label, $description, $parent_label])
        {

            $category = new Category();
            $category->setLabel($label);
            $category->setDescription($description);

            $parent = $CatRepo->findOneBy(['label' => $parent_label]);
            $parent->addSubCategory($category);

            // there's a cascade persist on deck-cards which avoids persisting down the relation
            $manager->persist($category);
        }
        $manager->flush();

        foreach (self::CardsGenerator() as [$card_name, $card_cats, $deck_name])
        {
            $deck = $deckRepo->findOneBy(['d_name' => $deck_name]);

            $card = new Card();
            $card->setCardName($card_name);

            $cats_labels= explode(' ',$card_cats);

            foreach($cats_labels as $cat_label) 
            {   
                $cat = $CatRepo->findOneBy(['label' => $cat_label]);
                $card->addCategory($cat);}

            $deck->addCard($card);
            // there's a cascade persist on deck-cards which avoids persisting down the relation
            $manager->persist($card);
        }
        $manager->flush();
    }
}

