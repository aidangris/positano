<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Positano implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */
declare(strict_types=1);

namespace Bga\Games\Positano;

use PSpell\Dictionary;

require_once(APP_GAMEMODULE_PATH . "module/table/table.game.php");

class Game extends \Table
{
    private static array $BID_TYPES;
    private static array $BOOSTER_TYPES;
    private static array $LOT_TYPES;
    private static array $LOT_TYPES_BB;
    private static array $BLOCK_TYPES;
    private static array $BLOCK_TYPES_BB;
    private static array $QUALITY_TYPES;
    private static array $QUALITY_TYPES_BB;
    private static array $GOAL_TYPES;

    private static array $PLAYER_BIDS = [];
    private static array $PLAYER_LOTS = [];

   private static array $BLOCK_COUNT = [];
   
    private static bool $SOLO = false;
    


    //private array $PLAYER_BIDS;

    /**
     * Your global variables labels:
     *
     * Here, you can assign labels to global variables you are using for this game. You can use any number of global
     * variables with IDs between 10 and 99. If your game has options (variants), you also have to associate here a
     * label to the corresponding ID in `gameoptions.inc.php`.
     *
     * NOTE: afterward, you can get/set the global variables with `getGameStateValue`, `setGameStateInitialValue` or
     * `setGameStateValue` functions.
     */
    public function __construct()
    {
        parent::__construct();

        $this->initGameStateLabels([
            "prev_state" => 10,
            "current_player" => 11,
            "player_bids" => 12,
            "player_lot" => 13,
            "big_board" => 101,
        ]);       
        
        $this->cards = $this->getNew( "module.common.deck" );
        $this->cards->init( "card" );

        self::$BID_TYPES = [
            1 => [
                "card_name" => '60 / 50 / 10', // ...
                "points" => 1,
                "1" => '0 / 4 / 5',
                "2" => '6 / 3 / 0',
                "3" => '5 / 2 / 2',
                "4" => '4 / 1 / 4',
                "5" => '3 / 0 / 6',
                "6" => '2 / 6 / 1',
            ],
            2 => [
                "card_name" => '10 / 60 / 50', // ...
                "points" => 1,
                "1" => '1 / 5 / 3',
                "2" => '0 / 4 / 5',
                "3" => '6 / 3 / 0',
                "4" => '5 / 2 / 2',
                "5" => '4 / 1 / 4',
                "6" => '3 / 0 / 6',
            ],
            3 => [
                "card_name" => '50 / 10 / 60', // ...
                "points" => 1,
                "1" => '2 / 6 / 1',
                "2" => '1 / 5 / 3',
                "3" => '0 / 4 / 5',
                "4" => '6 / 3 / 0',
                "5" => '5 / 2 / 2',
                "6" => '4 / 1 / 4',
            ],
            4 => [
                "card_name" => '40 / 40 / 40', // ...
                "points" => 1,
                "1" => '3 / 0 / 6',
                "2" => '2 / 6 / 1',
                "3" => '1 / 5 / 3',
                "4" => '0 / 4 / 5',
                "5" => '6 / 3 / 0',
                "6" => '5 / 2 / 2',
            ],
            5 => [
                "card_name" => '70 / 20 / 30', // ...
                "points" => 0,
                "1" => '4 / 1 / 4',
                "2" => '3 / 0 / 6',
                "3" => '2 / 6 / 1',
                "4" => '1 / 5 / 3',
                "5" => '0 / 4 / 5',
                "6" => '6 / 3 / 0',
            ],
            6 => [
                "card_name" => '30 / 70 / 20', // ...
                "points" => 0,
                "1" => '5 / 2 / 2',
                "2" => '4 / 1 / 4',
                "3" => '3 / 0 / 6',
                "4" => '2 / 6 / 1',
                "5" => '1 / 5 / 3',
                "6" => '0 / 4 / 5',
            ],
            7 => [
                "card_name" => '20 / 30 / 70', // ...
                "points" => 0,
                "1" => '6 / 3 / 0',
                "2" => '5 / 2 / 2',
                "3" => '4 / 1 / 4',
                "4" => '3 / 0 / 6',
                "5" => '2 / 6 / 1',
                "6" => '1 / 5 / 3',
            ],
        ];

        self::$BOOSTER_TYPES = [
            8 => [
                "card_name" => clienttranslate('5'), // ...
                "points" => 2,
            ],
            9 => [
                "card_name" => clienttranslate('6'), // ...
                "points" => 1,
            ],
            10 => [
                "card_name" => clienttranslate('13'), // ...
                "points" => 0,
            ],
            11 => [
                "card_name" => clienttranslate('14'), // ...
                "points" => 0,
            ],
            12 => [
                "card_name" => clienttranslate('21'), // ...
                "points" => 0,
            ],
            13 => [
                "card_name" => clienttranslate('22'), // ...
                "points" => 0,
            ],
        ];

        self::$LOT_TYPES = [
            20 => [
                "card_name" => clienttranslate('1A'), // ...
            ],
            21 => [
                "card_name" => clienttranslate('1B'), // ...
            ],
            22 => [
                "card_name" => clienttranslate('1C'), // ...
            ],
            23 => [
                "card_name" => clienttranslate('1D'), // ...
            ],
            24 => [
                "card_name" => clienttranslate('2A'), // ...
            ],
            25 => [
                "card_name" => clienttranslate('2B'), // ...
            ],
            26 => [
                "card_name" => clienttranslate('2C'), // ...
            ],
            27 => [
                "card_name" => clienttranslate('2D'), // ...
            ],
            28 => [
                "card_name" => clienttranslate('3A'), // ...
            ],
            29 => [
                "card_name" => clienttranslate('3B'), // ...
            ],
            30 => [
                "card_name" => clienttranslate('3C'), // ...
            ],
            31 => [
                "card_name" => clienttranslate('3D'), // ...
            ],
            32 => [
                "card_name" => clienttranslate('4A'), // ...
            ],
            33 => [
                "card_name" => clienttranslate('4B'), // ...
            ],
            34 => [
                "card_name" => clienttranslate('4C'), // ...
            ],
            35 => [
                "card_name" => clienttranslate('4D'), // ...
            ],
        ];

        self::$LOT_TYPES_BB = [
            20 => [
                "card_name" => clienttranslate('1A'), // ...
                "points" => 0,
            ],
            21 => [
                "card_name" => clienttranslate('1B'), // ...
                "points" => 0,
            ],
            22 => [
                "card_name" => clienttranslate('1C'), // ...
                "points" => 0,
            ],
            23 => [
                "card_name" => clienttranslate('1D'), // ...
                "points" => 0,
            ],
            24 => [
                "card_name" => clienttranslate('1E'), // ...
                "points" => 0,
            ],
            25 => [
                "card_name" => clienttranslate('2A'), // ...
                "points" => 0,
            ],
            26 => [
                "card_name" => clienttranslate('2B'), // ...
                "points" => 0,
            ],
            27 => [
                "card_name" => clienttranslate('2C'), // ...
                "points" => 0,
            ],
            28 => [
                "card_name" => clienttranslate('2D'), // ...
                "points" => 0,
            ],
            29 => [
                "card_name" => clienttranslate('2E'), // ...
                "points" => 0,
            ],
            30 => [
                "card_name" => clienttranslate('3A'), // ...
                "points" => 0,
            ],
            31 => [
                "card_name" => clienttranslate('3B'), // ...
                "points" => 0,
            ],
            32 => [
                "card_name" => clienttranslate('3C'), // ...
                "points" => 0,
            ],
            33 => [
                "card_name" => clienttranslate('3D'), // ...
                "points" => 0,
            ],
            34 => [
                "card_name" => clienttranslate('3E'), // ...
                "points" => 0,
            ],
            35 => [
                "card_name" => clienttranslate('4A'), // ...
                "points" => 0,
            ],
            36 => [
                "card_name" => clienttranslate('4B'), // ...
                "points" => 0,
            ],
            37 => [
                "card_name" => clienttranslate('4C'), // ...
                "points" => 0,
            ],
            38 => [
                "card_name" => clienttranslate('4D'), // ...
                "points" => 0,
            ],
            39 => [
                "card_name" => clienttranslate('4E'), // ...
                "points" => 0,
            ],
            40 => [
                "card_name" => clienttranslate('5A'), // ...
                "points" => 1,
            ],
            41 => [
                "card_name" => clienttranslate('5B'), // ...
                "points" => 1,
            ],
            42 => [
                "card_name" => clienttranslate('5C'), // ...
                "points" => 1,
            ],
            43 => [
                "card_name" => clienttranslate('5D'), // ...
                "points" => 1,
            ],
            44 => [
                "card_name" => clienttranslate('5E'), // ...
                "points" => 1,
            ],
        ];

        self::$BLOCK_TYPES = [
            40 => [
                "card_name" => clienttranslate('2 Blocks'), 
                "points" => 1,
            ],
            41 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            42 => [
                "card_name" => clienttranslate('4 Blocks'), // ...
                "points" => 0,
            ],
            43 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            44 => [
                "card_name" => clienttranslate('3x Blocks / 3 Blocks'), // ...
                "points" => 0,
            ],
            45 => [
                "card_name" => clienttranslate('4 Blocks'), // ...
                "points" => 0,
            ],
            46 => [
                "card_name" => clienttranslate('5 Blocks'), // ...
                "points" => 0,
            ],
            47 => [
                "card_name" => clienttranslate('5 Blocks'), // ...
                "points" => 0,
            ],
            48 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            49 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            50 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            51 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            52 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            53 => [
                "card_name" => clienttranslate('2x Blocks / 2 Blocks'), // ...
                "points" => 0,
            ],
            54 => [
                "card_name" => clienttranslate('1 Block'), // ...
                "points" => 2,
            ],
            55 => [
                "card_name" => clienttranslate('1 Block'), // ...
                "points" => 2,
            ],
        ];

        self::$BLOCK_TYPES_BB = [
            40 => [
                "card_name" => clienttranslate('2 Blocks'), 
                "points" => 1,
            ],
            41 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            42 => [
                "card_name" => clienttranslate('4 Blocks'), // ...
                "points" => 0,
            ],
            43 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            44 => [
                "card_name" => clienttranslate('5 Blocks'), // ...
                "points" => 0,
            ],
            45 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            46 => [
                "card_name" => clienttranslate('3x Blocks / 3 Blocks'), // ...
                "points" => 0,
            ],
            47 => [
                "card_name" => clienttranslate('4 Blocks'), // ...
                "points" => 0,
            ],
            48 => [
                "card_name" => clienttranslate('5 Blocks'), // ...
                "points" => 0,
            ],
            49 => [
                "card_name" => clienttranslate('5 Blocks'), // ...
                "points" => 0,
            ],
            50 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            51 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            52 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            53 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            54 => [
                "card_name" => clienttranslate('2 Blocks'), // ...
                "points" => 1,
            ],
            55 => [
                "card_name" => clienttranslate('2x Blocks / 2 Blocks'), // ...
                "points" => 0,
            ],
            56 => [
                "card_name" => clienttranslate('1 Block'), // ...
                "points" => 2,
            ],
            57 => [
                "card_name" => clienttranslate('1 Block'), // ...
                "points" => 2,
            ],
            58 => [
                "card_name" => clienttranslate('2x Blocks / 2 Blocks'), // ...
                "points" => 0,
            ],
            59 => [
                "card_name" => clienttranslate('2x Blocks / 2 Blocks'), // ...
                "points" => 0,
            ],
            60 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            61 => [
                "card_name" => clienttranslate('3 Blocks'), // ...
                "points" => 0,
            ],
            62 => [
                "card_name" => clienttranslate('4 Blocks'), // ...
                "points" => 0,
            ],
            63 => [
                "card_name" => clienttranslate('4 Blocks'), // ...
                "points" => 0,
            ],
            64 => [
                "card_name" => clienttranslate('5 Blocks'), // ...
                "points" => 0,
            ],
        ];

        self::$QUALITY_TYPES = [
            60 => [
                "card_name" => clienttranslate('Bronze'), // ...
                "points" => 2,
            ],
            61 => [
                "card_name" => clienttranslate('Silver'), // ...
                "points" => 0,
            ],
            62 => [
                "card_name" => clienttranslate('Gold'), // ...
                "points" => 0,
            ],
            63 => [
                "card_name" => clienttranslate('Bronze +1 Build Limit'), // ...
                "points" => 0,
            ],
            64 => [
                "card_name" => clienttranslate('Bronze'), // ...
                "points" => 2,
            ],
            65 => [
                "card_name" => clienttranslate('Silver +1 Build Limit'), // ...
                "points" => 0,
            ],
            66 => [
                "card_name" => clienttranslate('Bronze +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            67 => [
                "card_name" => clienttranslate('Bronze +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            68 => [
                "card_name" => clienttranslate('Silver +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            69 => [
                "card_name" => clienttranslate('Silver +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            70 => [
                "card_name" => clienttranslate('Gold +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            71 => [
                "card_name" => clienttranslate('Gold +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            72 => [
                "card_name" => clienttranslate('Bronze +2 Build Limit'), // ...
                "points" => 0,
            ],
            73 => [
                "card_name" => clienttranslate('Gold +1 Build Limit'), // ...
                "points" => 0,
            ],
            74 => [
                "card_name" => clienttranslate('Gold + Remodel'), // ...
                "points" => 0,
            ],
            75 => [
                "card_name" => clienttranslate('Silver + Remodel'), // ...
                "points" => 0,
            ],
        ];

        self::$QUALITY_TYPES_BB = [
            60 => [
                "card_name" => clienttranslate('Bronze'), // ...
                "points" => 2,
            ],
            61 => [
                "card_name" => clienttranslate('Silver'), // ...
                "points" => 0,
            ],
            62 => [
                "card_name" => clienttranslate('Gold'), // ...
                "points" => 0,
            ],
            63 => [
                "card_name" => clienttranslate('Bronze +1 Build Limit'), // ...
                "points" => 0,
            ],
            64 => [
                "card_name" => clienttranslate('Silver +1 Build Limit'), // ...
                "points" => 0,
            ],
            65 => [
                "card_name" => clienttranslate('Gold +1 Build Limit'), // ...
                "points" => 0,
            ],
            66 => [
                "card_name" => clienttranslate('Bronze'), // ...
                "points" => 2,
            ],
            67 => [
                "card_name" => clienttranslate('Silver +1 Build Limit'), // ...
                "points" => 0,
            ],
            68 => [
                "card_name" => clienttranslate('Silver +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            69 => [
                "card_name" => clienttranslate('Bronze +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            70 => [
                "card_name" => clienttranslate('Silver +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            71 => [
                "card_name" => clienttranslate('Silver +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            72 => [
                "card_name" => clienttranslate('Gold +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            73 => [
                "card_name" => clienttranslate('Gold +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            74 => [
                "card_name" => clienttranslate('Bronze +2 Build Limit'), // ...
                "points" => 0,
            ],
            75 => [
                "card_name" => clienttranslate('Gold +1 Build Limit'), // ...
                "points" => 0,
            ],
            76 => [
                "card_name" => clienttranslate('Gold + Remodel'), // ...
                "points" => 0,
            ],
            77 => [
                "card_name" => clienttranslate('Silver + Remodel'), // ...
                "points" => 0,
            ],
            78 => [
                "card_name" => clienttranslate('Silver +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            79 => [
                "card_name" => clienttranslate('Gold'), // ...
                "points" => 0,
            ],
            80 => [
                "card_name" => clienttranslate('Bronze +2 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
            81 => [
                "card_name" => clienttranslate('Gold + Remodel'), // ...
                "points" => 0,
            ],
            82 => [
                "card_name" => clienttranslate('Silver + Remodel'), // ...
                "points" => 0,
            ],
            83 => [
                "card_name" => clienttranslate('Bronze'), // ...
                "points" => 2,
            ],
            84 => [
                "card_name" => clienttranslate('Bronze +1 Build Limit + Add-On'), // ...
                "points" => 0,
            ],
        ];

        self::$GOAL_TYPES = [
            80 => [
                "card_name" => clienttranslate('Grande'), //most buildings above regulation height
            ],
            81 => [
                "card_name" => clienttranslate('Solitario'), //earn for each independent building
            ],
            82 => [
                "card_name" => clienttranslate('Eccesso'), //most leftover blocks
            ],
            83 => [
                "card_name" => clienttranslate('Dietro'), //most buildings on last 2 rows
            ],
            84 => [
                "card_name" => clienttranslate('Bordo'), //most buildings on outer columns
            ],
            85 => [
                "card_name" => clienttranslate('Perfetto'), //earn for each building at regulation height
            ],
            86 => [
                "card_name" => clienttranslate('Piccolo'), //earn for each building under regulation height
            ],
            87 => [
                "card_name" => clienttranslate('Gruppo'), //largest group of contiguous buildings
            ],
            88 => [
                "card_name" => clienttranslate('Equilibrato'), //earn for having at least 1 of each roof type
            ],
            89 => [
                "card_name" => clienttranslate('Colonna'), //most buildings in a single column
            ],
            90 => [
                "card_name" => clienttranslate('Mezzo'), //most buildings on inner columns
            ],
            91 => [
                "card_name" => clienttranslate('Altezza'), //earn for each different height among your buildings
            ],
        ];
        
        //self::$PLAYER_BIDS = [
            
        //];

        /* example of notification decorator.
        // automatically complete notification args when needed
        $this->notify->addDecorator(function(string $message, array $args) {
            if (isset($args['player_id']) && !isset($args['player_name']) && str_contains($message, '${player_name}')) {
                $args['player_name'] = $this->getPlayerNameById($args['player_id']);
            }
        
            if (isset($args['card_id']) && !isset($args['card_name']) && str_contains($message, '${card_name}')) {
                $args['card_name'] = self::$CARD_TYPES[$args['card_id']]['card_name'];
                $args['i18n'][] = ['card_name'];
            }
            
            return $args;
        });*/
    }
    

    function stMultiPlayerInit() {
        $this->gamestate->setAllPlayersMultiactive();
        
        
    }

    /**
     * Player action, example content.
     *
     * In this scenario, each time a player plays a card, this method will be called. This method is called directly
     * by the action trigger on the front side with `bgaPerformAction`.
     *
     * @throws BgaUserException
     */
    
    public function actOttoBid():void {
        //play bid for otto at the start of bids
        $this->cards->shuffle(0);
        $otto_cards = $this->cards->getCardsInLocation( 0 );
        
        $bid_id = 0;
        $booster_id = 0;
        $chosen_cards = [];
        foreach($otto_cards as $card){
            if ((int)$card['type_arg'] < 8){
                $chosen_cards[0] = $card['id'];
                $bid_id = (int)$card['type_arg'];
            }
            if ((int)$card['type_arg'] > 7){
                $chosen_cards[1] = $card['id'];
                $booster_id = (int)$card['type_arg'];
            }
        }
        
        
        
        $this->cards->moveCards($chosen_cards, 'discard');
            
        
        $bid_name = self::$BID_TYPES[$bid_id]['card_name'];
        $bid_points = self::$BID_TYPES[$bid_id]['points'];
        $booster_name = self::$BOOSTER_TYPES[$booster_id]['card_name'];
        $booster_points = self::$BOOSTER_TYPES[$booster_id]['points'];
        $player_id = 0;

        //$notification = ' plays a ' . $bid_name . ' bid with a +' . $booster_name . ' booster';

        $bids = explode(' / ', $bid_name);
        for ($i = 0; $i < count($bids); $i++){
            $bids[$i] = (int)$bids[$i] + (int)($booster_name);
        }
        $this->notify->all('bidPlayed', clienttranslate($this->ottoName() . ' plays bid ' . $bid_name . ' and booster ' . $booster_name), [
            'player_name' => 'Otto',
            'player_id' => $player_id,
            'color' => $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0],
        ]);

        $query_values = [];
        
        $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                $bids[0],
                $bids[1],
                $bids[2],
                $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0],
                $bid_id,
            ]);
        
        
        static::DbQuery(
            sprintf(
                "INSERT INTO bids (player, lot_bid, block_bid, quality_bid, color, bid_id) VALUES %s",
                implode(",", $query_values)
            )
        );
        $bids = $this->getObjectListFromDb(
            "SELECT `player`, `lot_bid`, `block_bid`, `quality_bid`, `color`, `num_played`, `bid_id` FROM `bids`"
        );
        $this->notify->all("stockBidTracker", '', [
            'bids' => $bids,
            'bid_types' => SELF::$BID_TYPES,
        ]);
        
        $points = $bid_points + $booster_points;
        if ($points > 0){
            $this->give_points($player_id, $points);
        }
        $this->gamestate->nextState("ottoPlayBid");
    }

    public function actPlayBid(int $bid_id, int $booster_id): void
    {
        
        
        // TODO update notifications to add player color and award points with notifications

        // Add your game logic to play a card here.
        $bid_name = self::$BID_TYPES[$bid_id]['card_name'];
        $bid_points = self::$BID_TYPES[$bid_id]['points'];
        $booster_name = self::$BOOSTER_TYPES[$booster_id]['card_name'];
        $booster_points = self::$BOOSTER_TYPES[$booster_id]['points'];
        $player_id = $this->getCurrentPlayerId();
        $player_name = $this->getCurrentPlayerName();

        
        
        // insert notification into database
        $notification = ' plays a ' . $bid_name . ' bid with a +' . $booster_name . ' booster';
        $query_values[] = vsprintf("('%s', '%s')", [
                $player_id,
                $notification,
            ]);
        
        static::DbQuery(
            sprintf(
                "INSERT INTO notifications (player, notification) VALUES %s",
                implode(",", $query_values)
            )
        );
        
        /*$this->notify->all("bidPlayed", clienttranslate('${player_name} plays a ${bid_name} bid with a +${booster_name} booster'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, // remove this line if you uncomment notification decorator
                    "bid_name" => $bid_name, // remove this line if you uncomment notification decorator
                    "booster_name" => $booster_name,
                    "i18n" => ['bid_name'], // remove this line if you uncomment notification decorator
        ]);*/
        

        //calculate bids
        
        $bids = explode(' / ', $bid_name);
        for ($i = 0; $i < count($bids); $i++){
            $bids[$i] = (int)$bids[$i] + (int)($booster_name);
        }

        self::$PLAYER_BIDS[] = [$player_id, $bids];
        $query_values = [];
        
        $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                $bids[0],
                $bids[1],
                $bids[2],
                $this->getAllDatas()['players'][$player_id]['color'],
                $bid_id,
            ]);
        
        
        static::DbQuery(
            sprintf(
                "INSERT INTO bids (player, lot_bid, block_bid, quality_bid, color, bid_id) VALUES %s",
                implode(",", $query_values)
            )
        );
        /*static::DbQuery(
                    sprintf(
                        "INSERT INTO removed (player, lot_id) VALUES %s",
                        "('" .(string)$player_id . "', '" . (string)$bid_id . "')"
                    )
                );*/
        
        $chosen_bid = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$bid_id' AND `card_location` = '$player_id'", true);
        $this->cards->moveCard($chosen_bid[0], 'discard');
        $chosen_bid = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$booster_id' AND `card_location` = '$player_id'", true);
        $this->cards->moveCard($chosen_bid[0], 'discard');
        
        //array_push(self::$PLAYER_BIDS, [$player_id, $bids]);//add bids to table

        //$this->notify->all('test', clienttranslate((string)count($this->getAllDatas()['notifications'])));
        
        if (count($this->getAllDatas()['notifications']) == count($this->getAllDatas()['players'])){//notify players once everyone has bid
           
            $this->notify_players();
        }

        $points = $bid_points + $booster_points;
        if ($points > 0){
            $this->give_points($player_id, $points);
        }

        // at the end of the action, move to the next state
        $this->setGameStateValue('prev_state', 2);
        
        $this->gamestate->setPlayerNonMultiactive($player_id, 'playBid'); // deactivate player; if none left, transition to 'next' state
        
    
    }

    public function actPass(bool $otto = false): void
    {
        // Retrieve the active player ID.
        if ($otto){
            $player_id = 0;
            $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `otto`")['q'];
            $player_name = $this->ottoName();
        } else {
            $player_id = (int)$this->getActivePlayerId();
            $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `player` WHERE `player_id` = '$player_id'")['q'];
            $player_name = $this->getActivePlayerName();
        }
        $this->removeQuality($quality_id);

        // Notify all players about the choice to pass.
        $this->notify->all("pass", clienttranslate('${player_name} passes'), [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "quality_id" => $quality_id,
        ]);

        // at the end of the action, move to the next state
        $this->gamestate->nextState("cantPlay");
    }

    public function actPickLot(int $lot_id): void
    {
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        array_push(self::$PLAYER_LOTS, $lot_id);
        $lot_name = $this->get_lot($lot_id);
        $color = $this->getAllDatas()['players'][$player_id]['color'];
        if ($lot_id > 39){ //give 1 point if lot is in 5th row
            $this->give_points($player_id, 1);
        }
        $buildings = $this->getAllDatas()['buildings'];

        $this->notify->all("lotPicked", clienttranslate('${player_name} picks lot ${lot_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, 
                    "lot_name" => $lot_name, 
                    "id" => $lot_id,
                    "color" => $color,
                    "buildings" => $buildings,
        ]);

        
        static::DbQuery(  "UPDATE `player` SET `lot_pick` = '$lot_id' WHERE `player_id` = '$player_id'");
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 1 WHERE `player` = '$player_id'");
        $chosen_lot = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$lot_id'", true);
        $this->cards->moveCard($chosen_lot[0], 'discard');

        $this->gamestate->nextState("pickLot");
    }

    public function actOttoPickLot(): void
    {
        $player_id = 0;
        $player_name = 'Otto';
        $lot_cards = $this->cards->getCardsInLocation('lot_stock');
        foreach($lot_cards as $card){
            $lot_id = (int)$card['type_arg'];
            break;
        }
        
        $lot_name = $this->get_lot($lot_id);
        $color = $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0];
        $buildings = $this->getAllDatas()['buildings'];

        $this->notify->all("lotPicked", clienttranslate('${player_name} picks lot ${lot_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $this->ottoName(), 
                    "lot_name" => $lot_name, 
                    "id" => $lot_id,
                    "color" => $color,
                    "buildings" => $buildings,
        ]);

        
        static::DbQuery(  "UPDATE `otto` SET `lot_pick` = '$lot_id'");
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 1 WHERE `player` = '$player_id'");
        $chosen_lot = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$lot_id'", true);
        $this->cards->moveCard($chosen_lot[0], 'discard');

        $this->gamestate->nextState("pickLot");
    }

    public function actPickBlocks(int $id, int $selection): void
    {
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $block_name = $this->get_blocks($id)[0];
        $points = $this->get_blocks($id)[1];
        $block_supply = (int)$this->getAllDatas()['players'][$player_id]['block_supply'];
        if ($points > 0){
            $this->give_points($player_id, $points);
        }

        if (count(explode('/', $block_name)) > 1 && $selection < 0) { //notify player for block choice
            $this->notify->player($player_id, 'playerBlockChoice', '', [
                "player_id" => $player_id,
                "block_name" => $block_name,
                "blocks" => (int)$this->getAllDatas()['players'][$player_id]['blocks'],
                "block_supply" => $block_supply,
                "id" => $id,
            ]);
        } else {
            $player_blocks = (int)$this->getAllDatas()['players'][$player_id]['blocks'];
            if ($selection < 0 || $selection == 1){
                $block_val = (int)explode(' ', $block_name)[0];
                $block_val += (int)$this->getAllDatas()['players'][$player_id]['blocks'];
            } else {
                $block_val = (int)explode('x', $block_name)[0];
                $block_val = (int)$this->getAllDatas()['players'][$player_id]['blocks'] * $block_val;
            }
            $diff = $block_val - $player_blocks;
            
            if ($diff > $block_supply){ //make sure drawn blocks don't go over supply
                $diff = $block_supply;
                $block_val = $player_blocks + $diff;
            }

            $block_supply -= $diff;

            $this->notify->all("blocksPicked", clienttranslate('${player_name} picks ${block_name}'), [
                        "player_id" => $player_id,
                        "player_name" => $player_name, 
                        "block_name" => $block_name, 
                        "blocks" => $block_val,
                        "block_supply" => $block_supply,
                        "id" => $id,
                        "i18n" => ['bid_name'], // remove this line if you uncomment notification decorator
            ]);

            
            static::DbQuery(  "UPDATE `bids` SET `num_played` = 2 WHERE `player` = '$player_id'");
            $chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$id' AND `card_type` = 'blocks'", true);
            $this->cards->moveCard($chosen[0], 'discard');

            SELF::$BLOCK_COUNT[$player_id] = $block_val;
            static::DbQuery(  "UPDATE `player` SET `blocks` = '$block_val' WHERE `player_id` = '$player_id'");
            static::DbQuery(  "UPDATE `player` SET `block_supply` = '$block_supply' WHERE `player_id` = '$player_id'");

            $this->gamestate->nextState("pickBlocks");
        }
    }

    public function actOttoPickBlocks(): void
    {
        $player_id = 0;
        $player_name = 'Otto';
        $block_cards = $this->cards->getCardsInLocation('block_stock');
        foreach($block_cards as $card){
            $id = (int)$card['type_arg'];
            break;
        }
        $block_name = self::$BLOCK_TYPES[$id]['card_name'];
        $points = self::$BLOCK_TYPES[$id]['points'];
        $block_val = 0;
        if ($points > 0){
            $this->give_points($player_id, $points);
        }
        $block_count = (int)$this->getObjectListFromDb("SELECT `blocks` FROM `otto`", true)[0];
        $block_supply = (int)$this->getObjectListFromDb("SELECT `block_supply` FROM `otto`", true)[0];
        if (count(explode('/', $block_name)) > 1) {
            $card_amount = (int)explode('x', $block_name)[0]; //automatically calculate which blocks to select
            if ($block_count + $card_amount > $block_count * $card_amount) {
                $block_val = $block_count + $card_amount;
            } else {
                $block_val = $block_count * $card_amount;
            }
        } else {
            
            $block_val = (int)explode(' ', $block_name)[0];
            $block_val += $block_count;
        }

        $diff = $block_val - $block_count;
            
        if ($diff > $block_supply){ //make sure drawn blocks don't go over supply
            $diff = $block_supply;
            $block_val = $block_count + $diff;
        }

        $block_supply -= $diff;

        $this->notify->all("blocksPicked", clienttranslate('${player_name} picks ${block_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $this->ottoName(), 
                    "block_name" => $block_name, 
                    "blocks" => $block_val,
                    "block_supply" => $block_supply,
                    "id" => $id,
                    "i18n" => ['bid_name'], // remove this line if you uncomment notification decorator
        ]);

        
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 2 WHERE `player` = '$player_id'");
        $chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$id'", true);
        $this->cards->moveCard($chosen[0], 'discard');

        static::DbQuery(  "UPDATE `otto` SET `blocks` = '$block_val'");
        static::DbQuery(  "UPDATE `otto` SET `block_supply` = '$block_supply'");
        $this->gamestate->nextState("pickBlocks");
        
    }

    public function actPickQuality(int $id): void
    {
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();

        $quality_name = $this->get_quality($id)[0];
        $points = $this->get_quality($id)[1];
        if ($points > 0){
            $this->give_points($player_id, $points);
        }

        $this->notify->all("qualityPicked", clienttranslate('${player_name} picks quality ${quality_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, 
                    "quality_name" => $quality_name, 
                    "color" => $this->getObjectListFromDb("SELECT `real_color` FROM `player` WHERE `player_id` = '$player_id'", true)[0],
                    "id" => $id,
                    "i18n" => ['bid_name'], // remove this line if you uncomment notification decorator
        ]);
        static::DbQuery(  "UPDATE `player` SET `quality_pick` = '$quality_name' WHERE `player_id` = '$player_id'");
        static::DbQuery(  "UPDATE `player` SET `quality_id` = '$id' WHERE `player_id` = '$player_id'");
        
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 3 WHERE `player` = '$player_id'");

        //$chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$id'", true);
        //$this->cards->moveCard($chosen[0], 'discard');

        $this->gamestate->nextState("pickQuality");
    }

    public function actOttoPickQuality(): void
    {
        $player_id = 0;
        $player_name = "Otto";

        $quality_cards = $this->cards->getCardsInLocation('quality_stock');
        foreach($quality_cards as $card){
            $id = (int)$card['type_arg'];
            break;
        }

        $quality_name = self::$QUALITY_TYPES[$id]['card_name'];
        $points = self::$QUALITY_TYPES[$id]['points'];
        if ($points > 0){
            $this->give_points($player_id, $points);
        }

        $this->notify->all("qualityPicked", clienttranslate('${player_name} picks quality ${quality_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $this->ottoName(), 
                    "quality_name" => $quality_name, 
                    "color" => $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0],
                    "id" => $id,
        ]);
        static::DbQuery(  "UPDATE `otto` SET `quality_pick` = '$quality_name'");
        static::DbQuery(  "UPDATE `otto` SET `quality_id` = '$id'");
        
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 3 WHERE `player` = '$player_id'");

        //$chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$id'", true);
        //$this->cards->moveCard($chosen[0], 'discard');

        $this->gamestate->nextState("ottoPickQuality");
    }

    public function actBuild(int $lot_id, int $height): void 
    {
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $lot_name = $this->get_lot($lot_id);
        $quality_name = $this->getObjectFromDb("SELECT `quality_pick` `q` FROM `player` WHERE `player_id` = '$player_id'")['q'];
        $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `player` WHERE `player_id` = '$player_id'")['q'];
        $block_val = (int)$this->getAllDatas()['players'][$player_id]['blocks'];
        $block_val -= $height;
        static::DbQuery(  "UPDATE player SET `blocks` = '$block_val' WHERE `player_id` = '$player_id'");
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 4 WHERE `player` = '$player_id'");
        $quality = 0;
        $quality_word = '';
        if (str_contains($quality_name, 'Bronze')){
            $quality_word = 'Bronze';
            $quality = 1;
        } else if (str_contains($quality_name, 'Silver')){
            $quality_word = 'Silver';
            $quality = 2;
        } else {
            $quality_word = 'Gold';
            $quality = 3;
        }
        $max_height = (int)substr($lot_name, 0, 1);

        if ($height == 0){ //player has no blocks
            $this->notify->all("buildingSkipped", clienttranslate('Build skipped for ${player_name} because they are out of blocks'), [
                "player_id" => $player_id,
                "player_name" => $player_name, 
                "lot_id" => $lot_id,
            ]);
            $this->removeQuality($quality_id);
            static::DbQuery(
                sprintf(
                    "INSERT INTO removed (lot_id) VALUES %s",
                    "(" . $lot_id . ")",
                )
            );
            if (count(explode('Remo', $quality_name)) < 2){ //skip addon, but not remodel
                $this->gamestate->nextState("build");
            } else {
                $temp_q = $quality-1;
                $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id' AND `quality` = '$temp_q'", true);
                if (count($eligible_buildings) > 0){
                    $this->gamestate->nextState("remodel");
                } else {
                    $this->gamestate->nextState("build");
                }
            }
        } else {
        

            //$chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$quality_id' AND `card_type` = 'quality'", true);
            //$this->cards->moveCard($chosen[0], 'discard'); //move quality to discard

            
            $build_limit = 0;
            if (count(explode('Add', $quality_name)) > 1){
                $build_limit = (int)explode(' ', explode('+', $quality_name)[1])[0];
            }

            //place a building
                
            $points = $this->calculate_points($lot_id, $quality, $height);

            //select buildings less than max height
            $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id' AND `height` < `max_height` + '$build_limit'", true); //add-on eligible buildings
            

            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s')", [
                    $lot_id,
                    $player_id,
                    $height,
                    $max_height,
                    $quality,
                    $this->getAllDatas()['players'][$player_id]['color'],
                    $points,
                ]);
            
            
            
            //place a building if none exists
            $build_check = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `lot_id` = '$lot_id'", true);
            if (count($build_check) == 0){
                static::DbQuery(
                    sprintf(
                        "INSERT INTO building (lot_id, player, height, max_height, quality, color, points) VALUES %s",
                        implode(",", $query_values)
                    )
                );
            }

            $this->update_points($lot_id);

            $buildings = $this->getAllDatas()['buildings'];

            $this->notify->all("buildingPlaced", clienttranslate('${player_name} builds a ${quality_word} building in lot ${lot_name}'), [
                        "player_id" => $player_id,
                        "player_name" => $player_name, 
                        "quality_name" => $quality_name, 
                        "quality" => $quality,
                        "quality_word" => $quality_word,
                        "quality_id" => $quality_id,
                        "lot_name" => $lot_name,
                        "lot_id" => $lot_id,
                        "blocks" => $block_val,
                        "color" => $this->getAllDatas()['players'][$player_id]['color'],
                        "height" => $height,
                        "points" => $points,
                        "addon_buildings" => $eligible_buildings,
                        "buildings" => $buildings,
            ]);

            if (count(explode('Add', $quality_name)) > 1 && $block_val == 0){ //notify player if add-on skipped
                $this->notify->player($player_id, "addOnSkipped", clienttranslate('Add-on skipped for ${player_name} because they have 0 blocks'), [
                        "player_name" => $player_name,
                    ]);
            }
            
            if (count(explode('Add', $quality_name)) > 1 && $block_val > 0){  //add-on notif
                
                if (count($eligible_buildings) > 0){
                    
                    $this->gamestate->nextState("addOn");
                } else {
                    $this->notify->player($player_id, "addOnSkipped", clienttranslate('Add-on skipped for ${player_name} because there are no eligible buildings'), [
                        "player_name" => $player_name,
                    ]);
                    $this->removeQuality($quality_id);
                    $this->gamestate->nextState("build");
                }
            } else if (count(explode('Remo', $quality_name)) > 1){  //remodel notif
                $temp_q = $quality-1;
                $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id' AND `quality` = '$temp_q'", true);
                if (count($eligible_buildings) > 0){
                    $this->gamestate->nextState("remodel");
                } else {
                    $this->notify->player($player_id, "remodelSkipped", clienttranslate('Remodel skipped for ${player_name} because there are no eligible buildings'), [
                        "player_name" => $player_name,
                    ]);
                    $this->removeQuality($quality_id);
                    $this->gamestate->nextState("build");
                }
            } else {
                $this->removeQuality($quality_id);
                $this->gamestate->nextState("build");
            }
        }

    }

    public function actOttoBuild(int $lot_id, int $height): void 
    {
        $player_id = 0;
        $player_name = $this->ottoName();
        $lot_name = self::$LOT_TYPES[$lot_id]['card_name'];
        $quality_name = $this->getObjectFromDb("SELECT `quality_pick` `q` FROM `otto`")['q'];
        $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `otto`")['q'];
        $block_val = (int)$this->getObjectFromDb("SELECT `blocks` `b` FROM `otto`")['b'];
        $block_val -= $height;
        static::DbQuery(  "UPDATE `otto` SET `blocks` = '$block_val'");
        static::DbQuery(  "UPDATE `bids` SET `num_played` = 4 WHERE `player` = '$player_id'");
        $max_height = (int)substr($lot_name, 0, 1);
        $quality = 0;
        $quality_word = '';
        if (str_contains($quality_name, 'Bronze')){
            $quality_word = 'Bronze';
            $quality = 1;
        } else if (str_contains($quality_name, 'Silver')){
            $quality_word = 'Silver';
            $quality = 2;
        } else {
            $quality_word = 'Gold';
            $quality = 3;
        }
        
        
        //$chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$quality_id'", true);
        //$this->cards->moveCard($chosen[0], 'discard'); //move quality to discard
        if ($height == 0){ //player has no blocks
            $this->notify->all("buildingSkipped", clienttranslate('Build skipped for ${player_name} because they are out of blocks'), [
                "player_id" => $player_id,
                "player_name" => $player_name, 
                "lot_id" => $lot_id,
            ]);
            $this->removeQuality($quality_id);
            $this->gamestate->nextState("build");
        } else {

            $build_limit = 0;
            if (count(explode('Add', $quality_name)) > 1){
                $build_limit = (int)explode(' ', explode('+', $quality_name)[1]);
            }

            //place a building
                
            $points = $this->calculate_points($lot_id, $quality, $height);
            

            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s')", [
                    $lot_id,
                    $player_id,
                    $height,
                    $max_height,
                    $quality,
                    $this->getObjectFromDb("SELECT `color` `c` FROM `otto`")['c'],
                    $points,
                ]);
            
            //select buildings less than max height
            $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id' AND `height` < `max_height` + '$build_limit'", true); //add-on eligible buildings
            
            //place a building if none exists
            $build_check = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `lot_id` = '$lot_id'", true);
            if (count($build_check) == 0){
                static::DbQuery(
                    sprintf(
                        "INSERT INTO building (lot_id, player, height, max_height, quality, color, points) VALUES %s",
                        implode(",", $query_values)
                    )
                );
            }

            $this->update_points($lot_id);

            $buildings = $this->getAllDatas()['buildings'];

            $this->notify->all("buildingPlaced", clienttranslate('${player_name} builds a ${quality_word} building in lot ${lot_name}'), [
                        "player_id" => $player_id,
                        "player_name" => $player_name, 
                        "quality_name" => $quality_name, 
                        "quality" => $quality,
                        "quality_id" => $quality_id,
                        "quality_word" => $quality_word,
                        "lot_name" => $lot_name,
                        "lot_id" => $lot_id,
                        "blocks" => $block_val,
                        "color" => $this->getObjectFromDb("SELECT `color` `c` FROM `otto`")['c'],
                        "height" => $height,
                        "points" => $points,
                        "buildings" => $buildings,
            ]);

            $real_player_id = (int)$this->getActivePlayerId();
            if (count(explode('Add', $quality_name)) > 1 && $block_val == 0){ //notify player if add-on skipped
                $this->notify->player($real_player_id, "addOnSkipped", clienttranslate('Add-on skipped for ${player_name} because they have 0 blocks'), [
                        "player_name" => $player_name,
                    ]);
            }
            
            if (count(explode('Add', $quality_name)) > 1 && $block_val > 0){  //add-on notif
                
                if (count($eligible_buildings) > 0){
                    
                    $this->gamestate->nextState("addOn");
                } else {
                    $this->notify->player($real_player_id, "addOnSkipped", clienttranslate('Add-on skipped for ${player_name} because there are no eligible buildings'), [
                        "player_name" => $player_name,
                    ]);
                    $this->removeQuality($quality_id);
                    $this->gamestate->nextState("build");
                }
            } else if (count(explode('Remo', $quality_name)) > 1){  //remodel notif
                $temp_q = $quality-1;
                $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id' AND `quality` = '$temp_q'", true);
                if (count($eligible_buildings) > 0){
                    $this->gamestate->nextState("remodel");
                } else {
                    $this->notify->player($real_player_id, "remodelSkipped", clienttranslate('Remodel skipped for ${player_name} because there are no eligible buildings'), [
                        "player_name" => $player_name,
                    ]);
                    $this->removeQuality($quality_id);
                    $this->gamestate->nextState("build");
                }
            } else {
                $this->removeQuality($quality_id);
                $this->gamestate->nextState("build");
            }
        }
    }

    public function actAddOn(int $lot_id): void{
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $color = $this->getObjectListFromDb("SELECT `real_color` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `player` WHERE `player_id` = '$player_id'")['q'];
        $lot_name = $this->get_lot($lot_id);
        $block_val = (int)$this->getAllDatas()['players'][$player_id]['blocks'];
        $block_val -= 1;
        static::DbQuery(  "UPDATE player SET `blocks` = '$block_val' WHERE `player_id` = '$player_id'");
        
        //change building height
        $building_height = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        $building_quality = $this->getObjectListFromDb("SELECT `quality` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        $building_height += 1;
        static::DbQuery(  "UPDATE `building` SET `height` = '$building_height' WHERE `lot_id` = '$lot_id'");

        //update points
        $points = $this->calculate_points($lot_id, $building_quality, $building_height);

        static::DbQuery(  "UPDATE `building` SET `points` = '$points' WHERE `lot_id` = '$lot_id'");

        $buildings = $this->getAllDatas()['buildings'];

        $this->notify->all("buildingRennovated", clienttranslate('${player_name} adds 1 block to the building in lot ${lot_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, 
                    "quality" => $building_quality,
                    "lot_name" => $lot_name,
                    "lot_id" => $lot_id,
                    "blocks" => $block_val,
                    "height" => $building_height,
                    "points" => $points,
                    "color" => $color,
                    "buildings" => $buildings,
        ]);

        $this->update_points($lot_id);
        $this->removeQuality($quality_id);
        $this->gamestate->nextState("addOn");
    }

    public function actOttoAddOn(int $lot_id): void{
        $player_id = 0;
        $player_name = $this->ottoName();
        $color = $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0];
        $lot_name = self::$LOT_TYPES[$lot_id]['card_name'];
        $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `otto`")['q'];
        $block_val = (int)$this->getObjectListFromDb("SELECT `blocks` FROM `otto`", true)[0];
        $block_val -= 1;
        static::DbQuery(  "UPDATE `otto` SET `blocks` = '$block_val'");
        
        //change building height
        $building_height = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        $building_quality = $this->getObjectListFromDb("SELECT `quality` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        $building_height += 1;
        static::DbQuery(  "UPDATE `building` SET `height` = '$building_height' WHERE `lot_id` = '$lot_id'");

        //update points
        $points = $this->calculate_points($lot_id, $building_quality, $building_height);

        static::DbQuery(  "UPDATE `building` SET `points` = '$points' WHERE `lot_id` = '$lot_id'");
        $buildings = $this->getAllDatas()['buildings'];

        $this->notify->all("buildingRennovated", clienttranslate('${player_name} adds 1 block to the building in lot ${lot_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, 
                    "quality" => $building_quality,
                    "lot_name" => $lot_name,
                    "lot_id" => $lot_id,
                    "blocks" => $block_val,
                    "height" => $building_height,
                    "points" => $points,
                    "color" => $color,
                    "buildings" => $buildings,
        ]);

        $this->update_points($lot_id);
        $this->removeQuality($quality_id);
        $this->gamestate->nextState("addOn");
    }

    public function actRemodel(int $lot_id): void {
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $lot_name = $this->get_lot($lot_id);
        $quality_name = $this->getObjectFromDb("SELECT `quality_pick` `q` FROM `player` WHERE `player_id` = '$player_id'")['q'];
        $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `player` WHERE `player_id` = '$player_id'")['q'];
        $color = $this->getObjectListFromDb("SELECT `real_color` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $block_val = (int)$this->getAllDatas()['players'][$player_id]['blocks'];
        $quality = 0;
        $quality_word = '';
        if (str_contains($quality_name, 'Bronze')){
            $quality_word = 'Bronze';
            $quality = 1;
        } else if (str_contains($quality_name, 'Silver')){
            $quality_word = 'Silver';
            $quality = 2;
        } else {
            $quality_word = 'Gold';
            $quality = 3;
        }
        $max_height = (int)substr($lot_name, 0, 1);

        $building_height = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        $building_quality = $this->getObjectListFromDb("SELECT `quality` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];

        static::DbQuery(  "UPDATE `building` SET `quality` = '$quality' WHERE `lot_id` = '$lot_id'");

        $points = $this->calculate_points($lot_id, $quality, $building_height);

        static::DbQuery(  "UPDATE `building` SET `points` = '$points' WHERE `lot_id` = '$lot_id'");

        $buildings = $this->getAllDatas()['buildings'];

        $this->notify->all("buildingRennovated", clienttranslate('${player_name} upgrades the building in lot ${lot_name} to quality ${quality}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, 
                    "quality_name" => $quality_name, 
                    "quality" => $quality,
                    "lot_name" => $lot_name,
                    "lot_id" => $lot_id,
                    "blocks" => $block_val,
                    "height" => $building_height,
                    "points" => $points,
                    "color" => $color,
                    "buildings" => $buildings,
        ]);
        $this->update_player_points();
        $this->removeQuality($quality_id);

        $this->gamestate->nextState("remodel");
    }

    public function actOttoRemodel(int $lot_id): void {
        $player_id = 0;
        $player_name = $this->ottoName();
        $lot_name = self::$LOT_TYPES[$lot_id]['card_name'];
        $quality_name = $this->getObjectFromDb("SELECT `quality_pick` `q` FROM `otto`")['q'];
        $quality_id = $this->getObjectFromDb("SELECT `quality_id` `q` FROM `otto`")['q'];
        $color = $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0];
        $block_val = (int)$this->getObjectListFromDb("SELECT `blocks` FROM `otto`", true)[0];
        $quality = 0;
        $quality_word = '';
        if (str_contains($quality_name, 'Bronze')){
            $quality_word = 'Bronze';
            $quality = 1;
        } else if (str_contains($quality_name, 'Silver')){
            $quality_word = 'Silver';
            $quality = 2;
        } else {
            $quality_word = 'Gold';
            $quality = 3;
        }
        $max_height = (int)substr($lot_name, 0, 1);

        $building_height = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        $building_quality = $this->getObjectListFromDb("SELECT `quality` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];

        static::DbQuery(  "UPDATE `building` SET `quality` = '$quality' WHERE `lot_id` = '$lot_id'");

        $points = $this->calculate_points($lot_id, $quality, $building_height);

        static::DbQuery(  "UPDATE `building` SET `points` = '$points' WHERE `lot_id` = '$lot_id'");

        $buildings = $this->getAllDatas()['buildings'];

        $this->notify->all("buildingRennovated", clienttranslate('${player_name} upgrades the building in lot ${lot_name} to quality ${quality}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name, 
                    "quality_name" => $quality_name, 
                    "quality" => $quality,
                    "lot_name" => $lot_name,
                    "lot_id" => $lot_id,
                    "blocks" => $block_val,
                    "height" => $building_height,
                    "points" => $points,
                    "color" => $color,
                    "buildings" => $buildings,
        ]);
        $this->update_player_points();
        $this->removeQuality($quality_id);
        $this->gamestate->nextState("remodel");
    }

    /**
     * Game state arguments, example content.
     *
     * This method returns some additional information that is very specific to the `playerTurn` game state.
     *
     * @return array
     * @see ./states.inc.php
     */
    public function argPlayerTurn(): array
    {
        // Get some values from the current game situation from the database.
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $blocks = $this->getObjectListFromDB("SELECT `blocks` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $lot_pick = $this->getObjectListFromDB("SELECT `lot_pick` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $quality_pick = $this->getObjectListFromDB("SELECT `quality_pick` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $quality_id = $this->getObjectListFromDB("SELECT `quality_id` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $color = $this->getObjectListFromDB("SELECT `real_color` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $bid_count = count($this->getObjectListFromDB("SELECT `player` FROM `bids`", true));
        $bids = $this->getObjectListFromDb("SELECT `player`, `lot_bid`, `block_bid`, `quality_bid`, `color`, `num_played`, `bid_id` FROM `bids`");
        $buildings =  $this->getObjectListFromDb("SELECT `lot_id`, `player`, `height`, `max_height`, `quality`, `color`, `points` FROM `building`");
        //$lot_pick = $this->getAllDatas()['players'][$player_id]['lot_pick'];
        //$quality_pick =  $this->getAllDatas()['players'][$player_id]['quality_pick'];
        //$blocks = $this->getAllDatas()['players'][$player_id]['blocks'];
        
        
        return [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "blocks" => $blocks,
            "lot_id" => $lot_pick,
            "quality" => $quality_pick,
            "quality_id" => $quality_id,
            "color" => $color,
            "bid_count" => $bid_count,
            "bids" => $bids,
            "buildings" => $buildings,
        ];
    }

    public function argOttoTurn(): array
    {
        // Get some values from the current game situation from the database.
        $player_id = 0;
        $player_name = 'Otto';
        $blocks = $this->getObjectListFromDB("SELECT `blocks` FROM `otto`", true)[0];
        $lot_pick = $this->getObjectListFromDB("SELECT `lot_pick` FROM `otto`", true)[0];
        $quality_pick = $this->getObjectListFromDB("SELECT `quality_pick` FROM `otto`", true)[0];
        //$lot_pick = $this->getAllDatas()['players'][$player_id]['lot_pick'];
        //$quality_pick =  $this->getAllDatas()['players'][$player_id]['quality_pick'];
        //$blocks = $this->getAllDatas()['players'][$player_id]['blocks'];
        
        
        return [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "blocks" => $blocks,
            "lot_id" => $lot_pick,
            "quality" => $quality_pick,
        ];
    }

    public function argAddOn(): array {

        
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $blocks = $this->getObjectListFromDB("SELECT `blocks` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $lot_pick = $this->getObjectListFromDB("SELECT `lot_pick` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $quality_pick = $this->getObjectListFromDB("SELECT `quality_pick` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $build_limit = 0;
        if (count(explode('Add', $quality_pick)) > 1){
            $build_limit = (int)explode(' ', explode('+', $quality_pick)[1])[0];
        }
        
        $quality = (int)explode(" ", $quality_pick)[0];
        
        $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id`, `height` FROM `building` WHERE `player` = '$player_id' AND `height` < `max_height` + '$build_limit'");
        
        return [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "blocks" => $blocks,
            "lot_id" => $lot_pick,
            "quality" => $quality_pick,
            "buildings" => $eligible_buildings,
            "build_limit" => $build_limit,
        ];
    }

    public function argOttoAddOn(): array {

        
        $player_id = 0;
        $player_name = 'Otto';
        $blocks = $this->getObjectListFromDB("SELECT `blocks` FROM `otto`", true)[0];
        $lot_pick = $this->getObjectListFromDB("SELECT `lot_pick` FROM `otto`", true)[0];
        $quality_pick = $this->getObjectListFromDB("SELECT `quality_pick` FROM `otto`", true)[0];
        $build_limit = 0;
        if (count(explode('Add', $quality_pick)) > 1){
            $build_limit = (int)explode(' ', explode('+', $quality_pick)[1])[0];
        }
        
        $quality = (int)explode(" ", $quality_pick)[0];
        
        $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id`, `height` FROM `building` WHERE `player` = '$player_id' AND `height` < `max_height` + '$build_limit'");
        
        return [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "blocks" => $blocks,
            "lot_id" => $lot_pick,
            "quality" => $quality_pick,
            "buildings" => $eligible_buildings,
            "build_limit" => $build_limit,
        ];
    }

    public function argRemodel(): array {

        
        $player_id = (int)$this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();
        $blocks = $this->getObjectListFromDB("SELECT `blocks` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $lot_pick = $this->getObjectListFromDB("SELECT `lot_pick` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        $quality_pick = $this->getObjectListFromDB("SELECT `quality_pick` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
        
        $quality = 0;
        $quality_word = '';
        if (str_contains($quality_pick, 'Bronze')){
            $quality_word = 'Bronze';
            $quality = 1;
        } else if (str_contains($quality_pick, 'Silver')){
            $quality_word = 'Silver';
            $quality = 2;
        } else {
            $quality_word = 'Gold';
            $quality = 3;
        }

        $temp_q = $quality-1;
        $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id`, `height`, `quality` FROM `building` WHERE `player` = '$player_id' AND `quality` = '$temp_q'");
        
        
        return [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "blocks" => $blocks,
            "lot_id" => $lot_pick,
            "quality" => $quality_pick,
            "buildings" => $eligible_buildings,
        ];
    }

    public function argOttoRemodel(): array {

        
        $player_id = 0;
        $player_name = 'Otto';
        $blocks = $this->getObjectListFromDB("SELECT `blocks` FROM `otto`", true)[0];
        $lot_pick = $this->getObjectListFromDB("SELECT `lot_pick` FROM `otto`", true)[0];
        $quality_pick = $this->getObjectListFromDB("SELECT `quality_pick` FROM `otto`", true)[0];
        
        $quality = 0;
        $quality_word = '';
        if (str_contains($quality_pick, 'Bronze')){
            $quality_word = 'Bronze';
            $quality = 1;
        } else if (str_contains($quality_pick, 'Silver')){
            $quality_word = 'Silver';
            $quality = 2;
        } else {
            $quality_word = 'Gold';
            $quality = 3;
        }

        $temp_q = $quality-1;
        $eligible_buildings = $this->getObjectListFromDb("SELECT `lot_id`, `height`, `quality` FROM `building` WHERE `player` = '$player_id' AND `quality` = '$temp_q'");
        
        
        return [
            "player_id" => $player_id,
            "player_name" => $player_name,
            "blocks" => $blocks,
            "lot_id" => $lot_pick,
            "quality" => $quality_pick,
            "buildings" => $eligible_buildings,
        ];
    }


    /**
     * Compute and return the current game progression.
     *
     * The number returned must be an integer between 0 and 100.
     *
     * This method is called each time we are in a game state with the "updateGameProgression" property set to true.
     *
     * @return int
     * @see ./states.inc.php
     */
    public function getGameProgression()
    {
        $max_buildings = 0;
        switch(count($this->getObjectListFromDb("SELECT `player_id` FROM `player`", true))){
            case 1:
                $max_buildings = 12;
                break;
            case 2: 
                $max_buildings = 12;
                break;
            case 3: 
                $max_buildings = 15;
                break;
            case 4: 
                $max_buildings = 16;
                break;
            case 5: 
                $max_buildings = 25;
                break;
            case 6: 
                $max_buildings = 24;
                break;
        }
        $building_count = count($this->getObjectListFromDb("SELECT `lot_id` FROM `building`", true));

        $num = ((float)$building_count / (float)$max_buildings) * 100;

        return (int)$num;
    }

    /**
     * Game state action, example content.
     *
     * The action method of state `nextPlayer` is called everytime the current game state is set to `nextPlayer`.
     */
    public function stNextPlayer(): void {
        
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();


        // Give some extra time to the active player when he completed an action
        $this->giveExtraTime($player_id);
        //$this->activeNextPlayer();
        $player_count = count($this->getAllDatas()['players']);
        if ($player_count == 1){
            $player_count = 2;
        }
        switch($this->getGameStateValue('prev_state')){
            case '2': //pick lots

                $order = $this->playerOrder(0);
                $otto = false;
                
                //$this->notify->all("order", implode(", ", $order));
                if ($order[$this->getGameStateValue('current_player')] > 0){
                    $this->gamestate->changeActivePlayer($order[$this->getGameStateValue('current_player')]);
                } else { //do ottos turn if he's the active player
                    //$this->ottoPickLot();
                    //$this->setGameStateValue('current_player', $this->getGameStateValue('current_player') + 1);
                    $otto = true;
                }
                
                
                $this->setGameStateValue('current_player', $this->getGameStateValue('current_player') + 1);
                if ($this->getGameStateValue('current_player') >= $player_count || count($this->cards->getCardsInLocation('lot_stock')) == 0){
                    $this->setGameStateValue('current_player', 0);
                    $this->setGameStateValue('prev_state', 3);
                    //$this->stockBids();
                    /*if ($otto){
                        $this->gamestate->nextState('runitback');
                        break;
                    }*/
                }
                if ($otto){
                    $this->gamestate->nextState('ottoLot');
                } else {
                    $this->gamestate->nextState('nextLot');
                }
                
                break;

            case '3': //pick blocks
                $order = $this->playerOrder(1);
                $otto = false;
                
                //$this->notify->all("order", implode(", ", $order));
                if ($order[$this->getGameStateValue('current_player')] > 0){
                    $this->gamestate->changeActivePlayer($order[$this->getGameStateValue('current_player')]);
                } else { //do ottos turn if he's the active player
                    //$this->ottoPickBlocks();
                    //$this->setGameStateValue('current_player', $this->getGameStateValue('current_player') + 1);
                    $otto = true;
                }
                
                $this->setGameStateValue('current_player', $this->getGameStateValue('current_player') + 1);
                if ($this->getGameStateValue('current_player') >= $player_count || count($this->cards->getCardsInLocation('block_stock')) == 0){
                    $this->setGameStateValue('current_player', 0);
                    $this->setGameStateValue('prev_state', 4);
                    //$this->stockBids();
                    /*if ($otto){
                        $this->gamestate->nextState('runitback');
                        break;
                    }*/
                }
                if ($otto){
                    $this->gamestate->nextState('ottoBlock');
                } else {
                    $this->gamestate->nextState('nextBlock');
                }
                //$this->gamestate->nextState('nextBlock');
                break;
            case '4': //pick quality -> player turn
                $order = $this->playerOrder(2);
                $otto = false;
               
                
                if ($this->getGameStateValue('current_player') >= $player_count){
                    $this->setGameStateValue('current_player', 0);
                    $this->setGameStateValue('prev_state', 2);
                    static::DbQuery("DELETE FROM bids");
                    
                    //trigger endgame if no more lots
                    if (count($this->getAllDatas()['lots']) == 0 && count($this->getAllDatas()['lot_deck']) == 0) {
                        $goals = $this->cards->getCardsInLocation( 'goal_stock' ); //award goal points before winner is declared
                        foreach($goals as $goal) {
                            $id = $goal['type_arg'];
                            $goal_name = self::$GOAL_TYPES[$id]['card_name'];
                            $this->award_goal_points($goal_name);
                        }
                        
                        if (count($this->getAllDatas()['players']) == 1){
                            $player_score = (int)$this->getObjectListFromDb("SELECT `player_score` FROM `player`", true)[0];
                            $otto_score = (int)$this->getObjectListFromDb("SELECT `score` FROM `otto`", true)[0];
                            $score = 0;
                            if ($player_score > $otto_score){
                                $score = $otto_score;
                                static::DbQuery("UPDATE `player` SET `player_score` = '$score'");
                            } else {
                                $score = $player_score;
                            }
                            $this->notify->all('solo_endgame', '', [
                                "score" => $score,
                            ]);
                        }
                        $this->gamestate->nextState('endGame');
                        break;
                    }
                    $this->stock_stocks(count($this->getAllDatas()['players']), $this->getAllDatas()['players']);
                    
                    $this->gamestate->nextState("nextBid");
                    
                    
                    break;
                }

                if ($order[$this->getGameStateValue('current_player')] > 0){
                    $this->gamestate->changeActivePlayer($order[$this->getGameStateValue('current_player')]);
                } else { //do ottos turn if he's the active player
                    //$this->ottoPickQuality();
                    //$this->setGameStateValue('current_player', $this->getGameStateValue('current_player') + 1);
                    $otto = true;
                }
                $this->setGameStateValue('current_player', $this->getGameStateValue('current_player') + 1);
                if (!$otto){
                    $this->gamestate->nextState('nextQuality');
                } else {
                    $this->gamestate->nextState('ottoQuality');
                }
                
                break;

            
            
        }

       
        
        // Go to another gamestate
        // Here, we would detect if the game is over, and in this case use "endGame" transition instead 
        //
    }

    /*public function stGameEnd(): void {
        
    }*/


    //--------------------------------------------------------helper functions---------------------------------------------------

    //get player order based on bids

    public function playerOrder(int $state): array {
        $order = [];
        $bids = [];
        $key = "";
        $player_bids = $this->getAllDatas()["bids"];
        //$this->notify->all("order", implode(", ", $player_bids));

        switch($state){
            case 0:
                $key = "lot_bid";
                break;
            case 1:
                $key = "block_bid";
                break;
            case 2:
                $key = "quality_bid";
                break;
        }

        //get bids
        for ($i = 0; $i < count($player_bids); $i++) {
            $bid = $player_bids[$i][$key];
            array_push($bids, $bid);
        }
        //$this->notify->all('orderPicked', clienttranslate('bids chosen ' . implode(', ', $bids)));
        $bid_ids = [];
        rsort($bids);//sort bids descending
        for ($i = 0; $i < count($bids); $i++) {    //match players to highest bid and add to order
            for ($j = 0; $j < count($player_bids); $j++) {
                $player_id = $player_bids[$j]['player'];
                $bid_ids[$player_id] = $player_bids[$j]['bid_id'];
                $bid = $player_bids[$j][$key];
                if ($bid == $bids[$i] && !in_array($player_id, $order)){
                    array_push($order, $player_id);
                    if (count($bids) == 0) {
                        break;
                    }
                }
            }
            if (count($order) == count($player_bids)){
                break;
            }
            
        }

        while(true){
            $swap = 0;
            $continuous = 0;
            for ($i = 0; $i < count($bids)-1; $i++) { //break ties using alternate values
                if ($bids[$i] == $bids[$i + 1]){
                    $continuous ++;
                    $player_1 = $order[$i];
                    $player_2 = $order[$i+1];
                    if ($player_1 == 0){
                        $bid_set_1 = $this->getObjectListFromDb("SELECT `bid_set` FROM `otto`", true)[0];
                    } else {
                        $bid_set_1 = $this->getObjectListFromDb("SELECT `bid_set` FROM `player` WHERE `player_id` = '$player_1'", true)[0];
                    }
                    if ($player_2 == 0){
                        $bid_set_2 = $this->getObjectListFromDb("SELECT `bid_set` FROM `otto`", true)[0];
                    } else {
                        $bid_set_2 = $this->getObjectListFromDb("SELECT `bid_set` FROM `player` WHERE `player_id` = '$player_2'", true)[0];
                    }
                    $tb_1 = explode(' / ', self::$BID_TYPES[$bid_ids[$player_1]][$bid_set_1])[$state];
                    $tb_2 = explode(' / ', self::$BID_TYPES[$bid_ids[$player_2]][$bid_set_2])[$state];
                    if ($tb_2 > $tb_1){
                        $order[$i] = $player_2;
                        $order[$i+1] = $player_1;
                        $swap = 1;
                    }
                }
            }
            if ($swap == 0 || $continuous < 2){ //stop trying to break ties once no swaps have been made (or if only one tie exists)
                break;
            }
        }
        
        
        //$this->notify->all('orderPicked', clienttranslate('order chosen ' . implode(', ', $order)));
        return $order;
    }

    public function stockBids(): void {
        $bids = $this->getAllDatas()['bids'];
        $this->notify->all("stockBidTracker", '', [
            'bids' => $bids,
            'bid_types' => SELF::$BID_TYPES,
        ]);
    }

    //notify players of bids after they have been played
    public function notify_players(): void {
        $notifs = $this->getAllDatas()['notifications'];
        $bids = $this->getAllDatas()['bids'];
        $lot_bid = 0;
        $block_bid = 0;
        $quality_bid = 0;
        $bid_id = 0;
        
        for ($i = 0; $i < count($notifs); $i++) {
            $player_id = $notifs[$i]['player'];
            $tie = false;

            for ($j = 0; $j < count($bids); $j++){
                if ($bids[$j]['player'] == $player_id){
                    $lot_bid = $bids[$j]['lot_bid'];
                    $block_bid = $bids[$j]['block_bid'];
                    $quality_bid = $bids[$j]['quality_bid'];
                    $bid_id = $bids[$j]['bid_id'];

                     //detect tie
                     for ($p = 0; $p < count($bids); $p++){
                        if ($lot_bid == $bids[$p]['lot_bid'] && $j != $p){
                            $tie = true;
                        }
                     }
                    
                }
                
            }
            $bid_set = $this->getObjectListFromDb("SELECT `bid_set` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
            $alternates = self::$BID_TYPES[$bid_id][$bid_set];

            $this->notify->all("bidPlayed", clienttranslate('${player_name} ${notification}'), [
                'player_id' => $player_id,
                'player_name' => $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0],
                'notification' => $notifs[$i]['notification'],
                'lot_bid' => $lot_bid,
                'block_bid' => $block_bid,
                'quality_bid' => $quality_bid,
                'color' => $this->getObjectListFromDb("SELECT `color` FROM `bids` WHERE `player` = '$player_id'", true)[0],
                'tie' => $tie,
                'alternates' => $alternates,
            ]);
        }

        $this->notify->all("stockBidTracker", '', [
            'bids' => $bids,
            'bid_types' => SELF::$BID_TYPES,
        ]);

        static::DbQuery("DELETE FROM notifications");
        
    }

    public function ottoName(): string {
        $color = $this->getObjectListFromDb("SELECT `color` FROM `otto`", true)[0];
        switch ($color) {
            case 'blue':
                $color = '#5391ae';
                break;
            case 'green':
                $color = '#65a668';
                break;
            case 'red':
                $color = '#c25151';
                break;
            case 'yellow':
                $color = '#e2c742';
                break;
            case 'orange':
                $color = '#e1973f';
                break;
            case 'purple':
                $color = '#855fa3';
                break;
        }
        return '<b style="color: ' . $color . '">Otto</b>';
    }

    public function removeQuality($quality_id): void {
        $chosen = $this->getObjectListFromDb("SELECT `card_id` FROM `card` WHERE `card_type_arg` = '$quality_id' AND `card_type` = 'quality'", true);
        $this->cards->moveCard($chosen[0], 'discard'); //move quality to discard
        $this->notify->all("removeQuality", '', [
            "quality_id" => $quality_id
        ]);
    }

    
    public function stock_stocks($num, $players): void {
        //stock stocks
        if ($num == 1){
            $num = 2;
        }
        $lot_stock = $this->cards->pickCardsForLocation($num, 'lot_deck', 'lot_stock', 0, false );
        $block_stock = $this->cards->pickCardsForLocation($num, 'block_deck', 'block_stock', 0, false );
        $quality_stock = $this->cards->pickCardsForLocation($num, 'quality_deck', 'quality_stock', 0, false );
        $buildings =  $this->getObjectListFromDb(
            "SELECT `lot_id`, `player`, `height`, `max_height`, `quality`, `color`, `points` FROM `building`"
        );

        
        $this->notify->all("stocksStocked", '', [
            'lot_stock'=> $lot_stock,
            'block_stock'=> $block_stock,
            'quality_stock'=> $quality_stock,
            'buildings' => $buildings,
        ]);
    }

    public function calculate_points($lot_id, $quality, $height): int {
        //get buildings in front for points
        $points = 0;
        $blocking_height = 0;
        $row_size = 4;
        if ($this->getGameStateValue('big_board') == '1'){
            $row_size = 5;
        }
        if ($lot_id > 19 + $row_size){
            $blocking_lot = $lot_id - $row_size;
            $height_diff = 1;
            while($blocking_lot >= 20){
                $b = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$blocking_lot'", true);
                if (count($b) > 0){
                    $temp_height = $b[0] - $height_diff;
                    if ($temp_height > $blocking_height){
                        $blocking_height = $temp_height;
                    }
                }
                $blocking_lot -= $row_size;
                $height_diff += 1;
            }
        }
        $height -= $blocking_height;
        if ($height > 0){
            $points = $height * $quality;
        }
        return $points;
    }

    public function update_points($lot_id): void{
        $row_size = 4;
        $max_id = 36;
        $last_row = $max_id - $row_size;
        if ($this->getGameStateValue('big_board') == '1'){
            $row_size = 5;
            $max_id = 45;
            $last_row = $max_id - $row_size;
        }
        if ($lot_id < $last_row){
            $update_id = $lot_id + $row_size;
            while($update_id < $max_id) {
                $height = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$update_id'", true);
                if (count($height) > 0){
                    $quality = $this->getObjectListFromDb("SELECT `quality` FROM `building` WHERE `lot_id` = '$update_id'", true)[0];
                    $points = $this->calculate_points($update_id, $quality, $height[0]);
                    static::DbQuery(  "UPDATE `building` SET `points` = '$points' WHERE `lot_id` = '$update_id'");
                    $buildings = $this->getAllDatas()['buildings'];
                    
                    $this->notify->all("buildingRennovated", '', [
                        "player_id" => -1,
                        "quality" => $quality,
                        "lot_id" => $update_id,
                        "height" => $height[0],
                        "points" => $points,
                        "buildings" => $buildings,
                    ]);
                    
                    
                }
                $update_id += $row_size;
                //$this->notify->all("boingus", 'update id: ' . $update_id,[]);
            }
        }
        $this->update_player_points();
    }

    public function update_player_points(): void{
        foreach ($this->getAllDatas()['players'] as $player_id => $player) {
            $points = 0; //get building points
            $point_list = $this->getObjectListFromDb("SELECT `points` FROM `building` WHERE `player` = '$player_id'", true);
            for ($i = 0; $i < count($point_list); $i++){
                $points += $point_list[$i];
            }
            
            //update player buildings points
            static::DbQuery("UPDATE `player` SET `building_points` = '$points' WHERE `player_id` = '$player_id'");
            $this->setStat($points, 'building_points', $player_id);

            //add other points to total
            $points += $this->getObjectListFromDb("SELECT `points` FROM `player` WHERE `player_id` = '$player_id'", true)[0];

            //set score to calculate win
            static::DbQuery("UPDATE `player` SET `player_score` = '$points' WHERE `player_id` = '$player_id'");
            $this->notify->all('pointsUpdated', '', [
                "player_id" => $player_id,
                "points" => $points,
            ]);
        }
        if (count($this->getAllDatas()['players']) == 1){ //update otto points
            $player_id = 0;
            $points = 0; //get building points
            $point_list = $this->getObjectListFromDb("SELECT `points` FROM `building` WHERE `player` = '$player_id'", true);
            for ($i = 0; $i < count($point_list); $i++){
                $points += $point_list[$i];
            }
            
            //update player buildings points
            static::DbQuery("UPDATE `otto` SET `building_points` = '$points'");

            //add other points to total
            $points += $this->getObjectListFromDb("SELECT `points` FROM `otto`", true)[0];

            //set score to calculate win
            static::DbQuery("UPDATE `otto` SET `score` = '$points'");
            $this->notify->all('pointsUpdated', '', [
                "player_id" => $player_id,
                "points" => $points,
            ]);
        }
    }

    public function get_lot($lot_id): string {
        if ($this->getGameStateValue('big_board') == '1') {
            $lot = self::$LOT_TYPES_BB[$lot_id]['card_name'];
        } else {
            $lot = self::$LOT_TYPES[$lot_id]['card_name'];
        }
        return $lot;
    }

    public function get_blocks($id): array {
        if ($this->getGameStateValue('big_board') == '1') {
            $block = self::$BLOCK_TYPES_BB[$id]['card_name'];
            $points = self::$BLOCK_TYPES_BB[$id]['points'];
        } else {
            $block = self::$BLOCK_TYPES[$id]['card_name'];
            $points = self::$BLOCK_TYPES[$id]['points'];
        }
        return [$block, $points];
    }

    public function get_quality($id): array {
        if ($this->getGameStateValue('big_board') == '1') {
            $quality = self::$QUALITY_TYPES_BB[$id]['card_name'];
            $points = self::$QUALITY_TYPES_BB[$id]['points'];
        } else {
            $quality = self::$QUALITY_TYPES[$id]['card_name'];
            $points = self::$QUALITY_TYPES[$id]['points'];
        }
        return [$quality, $points];
    }

    public function give_points($player_id, int $points, $goal = false){
        if ($player_id > 0){
            $point_count = $this->getObjectListFromDb("SELECT `points` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
            $point_count += $points;
            if ($goal){
                $this->incStat($points, 'goal_points', $player_id);
            } else {
                $this->setStat($point_count, 'gelato_points', $player_id);
            }
            static::DbQuery("UPDATE `player` SET `points` = '$point_count' WHERE `player_id` = '$player_id'");
            $point_count += $this->getObjectListFromDb("SELECT `building_points` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
            static::DbQuery("UPDATE `player` SET `player_score` = '$point_count' WHERE `player_id` = '$player_id'");
            $this->notify->all('pointsUpdated', '', [
                    "player_id" => $player_id,
                    "points" => $point_count,
                ]);
        } else {
            $point_count = $this->getObjectListFromDb("SELECT `points` FROM `otto`", true)[0];
            $point_count += $points;
            static::DbQuery("UPDATE `otto` SET `points` = '$point_count'");
            $point_count += $this->getObjectListFromDb("SELECT `building_points` FROM `otto`", true)[0];
            static::DbQuery("UPDATE `otto` SET `score` = '$point_count'");
            $this->notify->all('pointsUpdated', '', [
                    "player_id" => $player_id,
                    "points" => $point_count,
                ]);
        }
        
    }

    public function check_independance(int $lot_id):bool{
        $row_size = 4;
        if ($this->getGameStateValue('big_board') == '1'){
            $row_size = 5;
        }
        $player_id = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        //check up
        $id = $lot_id + $row_size;
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)) > 0){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)[0]){
                return false;
            }
        }
    
        //check down
        $id = $lot_id - $row_size;
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)) > 0){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)[0]){
                return false;
            }
        }

         //check left
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' - 1", true)) > 0 && $lot_id % $row_size != 0){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' - 1", true)[0]){
                return false;
            }
        }
        
         //check right
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' + 1", true)) > 0 && $lot_id % $row_size != $row_size - 1){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' + 1", true)[0]){
                return false;
            }
        }
    
        return true;
    }

    public function check_group(int $lot_id, $group): array {
        $row_size = 4;
        if ($this->getGameStateValue('big_board') == '1'){
            $row_size = 5;
        }
        $player_id = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id'", true)[0];
        //check up
        $id = $lot_id + $row_size;
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)) > 0){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)[0]){
                if (array_search($id, $group, false) == false) {
                    array_push($group, $id);
                    $group = $this->check_group($id, $group);
                }
            }
        }
    
        //check down
        $id = $lot_id - $row_size;
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)) > 0){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$id'", true)[0]){
                if (array_search($id, $group, false) == false) {
                    array_push($group, $id);
                    $group = $this->check_group($id, $group);
                }
            }
        }

         //check left
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' - 1", true)) > 0 && $lot_id % $row_size != 0){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' - 1", true)[0]){
                if (array_search($lot_id - 1, $group, false) == false) {
                    array_push($group, $lot_id - 1);
                    $group = $this->check_group($lot_id - 1, $group);
                }
            }
        }
        
         //check right
        if (count($this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' + 1", true)) > 0 && $lot_id % $row_size != $row_size - 1){
            if ($player_id == $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` = '$lot_id' + 1", true)[0]){
                if (array_search($lot_id + 1, $group, false) == false) {
                    array_push($group, $lot_id + 1);
                    $group = $this->check_group($lot_id + 1, $group);
                }
            }
        }
        return $group;
    }

    public function get_group_heights($group): array {
        $heights = [];
        foreach($group as $id){
            array_push($heights, $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` = '$id'", true)[0]);
        }
        return $heights;
    }

    public function compare($buildings, $heights, $goal_name){
        $building_count = array_count_values($buildings);
        $first = [];
        $first_count = 0;
        $second = [];
        $second_count = 0;
        $temp_buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
        $player_ids = array_unique($temp_buildings);
        foreach ($player_ids as $player_id) {
            if (array_key_exists($player_id, $building_count)){
                $temp_count = $building_count[$player_id] * 100 + array_sum($heights[$player_id]);
                if ($temp_count > $first_count){ //put first place as first and replace second
                    $second_count = $first_count;
                    //$second = $first;
                    $first_count = $temp_count;
                    //$first = $player_id;
                } else if ($temp_count > $second_count && $temp_count < $first_count){ //put second place second
                    $second_count = $temp_count;
                    //$second = $player_id;
                }
            }
        }
        foreach ($player_ids as $player_id) {
            if (array_key_exists($player_id, $building_count)){
                $temp_count = $building_count[$player_id] * 100 + array_sum($heights[$player_id]);
                if ($temp_count == $first_count){
                    array_push($first, $player_id);
                } else if ($temp_count == $second_count){
                    array_push($second, $player_id);
                }
            }
        }
        $award_second = true;
        if (count($first) > 1 && $first_count > 0){ //split points for first tie
            $points = (int) (9 / count($first));
            foreach ($first as $player_id) {
                $player_name = '';
                if ((int)$player_id == 0){
                    $player_name = $this->ottoName();
                } else {
                    $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                }
                $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for ${goal_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name,
                    "points" => $points,
                    "goal_name" => $goal_name,
                ]);
                $this->give_points($player_id, $points, true);
            }
            $award_second = false;
        } else { //reward first points if no tie exists
            if ($first_count > 0){
                $player_id = $first[0];
                $player_name = '';
                if ((int)$player_id == 0){
                    $player_name = $this->ottoName();
                } else {
                    $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                }
                $this->notify->all("goalPoints", clienttranslate('${player_name} awarded 6 points for ${goal_name}'), [
                    "player_id" => $player_id,
                    "player_name" => $player_name,
                    "goal_name" => $goal_name,
                ]);
                $this->give_points($player_id, 6, true);
            }
        }
        if ($award_second) { //award points for second place only if first place isn't a tie
            if (count($second) > 1 && $second_count > 0){ //split points for second tie
                $points = (int) (3 / count($second));
                foreach ($second as $player_id) {
                    $player_name = '';
                    if ((int)$player_id == 0){
                        $player_name = $this->ottoName();
                    } else {
                        $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                    }
                    $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for ${goal_name}'), [
                        "player_id" => $player_id,
                        "player_name" => $player_name,
                        "points" => $points,
                        "goal_name" => $goal_name,
                    ]);
                    $this->give_points($player_id, $points, true);
                }
            } else { //award points regularly otherwise
                
                if ($second_count > 0 && $second_count != $first_count){
                    $player_id = $second[0];
                    $player_name = '';
                    if ((int)$player_id == 0){
                        $player_name = $this->ottoName();
                    } else {
                        $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                    }
                    $this->notify->all("goalPoints", clienttranslate('${player_name} awarded 3 points for ${goal_name}'), [
                        "player_id" => $player_id,
                        "player_name" => $player_name,
                        "goal_name" => $goal_name,
                    ]);
                    $this->give_points($player_id, 3, true);
                }
            }
        }
    }

    public function award_goal_points($goal_name){
        //calculate turn number
        $temp_buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
        $building_count = array_count_values($temp_buildings);
        $temp_count = 0;
        foreach ($building_count as $count ){
            if ($count > $temp_count){
                $temp_count = $count;
                $this->setStat($count, 'turns_number');
            }
        }

        $row_size = 4;
        if ($this->getGameStateValue('big_board') == '1'){
            $row_size = 5;
        }
        switch($goal_name){
            case "Bordo":   //most buildings on outer columns 6 / 3
                if ($row_size == 4){//small board
                    $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` % 4 = 0 OR `lot_id` % 4 = 3", true);
                    $heights = [];
                    $player_ids = array_unique($buildings);
                    foreach ($player_ids as $player_id) {
                        $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE (`lot_id` % 4 = 0 OR `lot_id` % 4 = 3) AND `player` = '$player_id'", true);
                    }
                } else {//big board
                    $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` % 5 = 0 OR `lot_id` % 5 = 4", true);
                    $heights = [];
                    $player_ids = array_unique($buildings);
                    foreach ($player_ids as $player_id) {
                        $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE (`lot_id` % 5 = 0 OR `lot_id` % 5 = 4) AND `player` = '$player_id'", true);
                    }
                }
                
                $this->compare($buildings, $heights, $goal_name);
                break;

            case "Solitario": //2 points per independant building
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $b = $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id'", true);
                    $points = 0;
                    for ($i = 0; $i < count($b); $i ++){
                        $id = (int)$b[$i];
                        //$player_id = $b[$i]['player'];
                        if ($this->check_independance($id)){
                            $points += 2;
                            /*$this->notify->all('test', clienttranslate('${id} is independant'), [
                                "id" => $id,
                            ]);*/
                        }
                    }
                    $player_name = '';
                    if ((int)$player_id == 0){
                        $player_name = $this->ottoName();
                    } else {
                        $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                    }
                    $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for Solitario'), [
                                "player_id" => $player_id,
                                "player_name" => $player_name,
                                "points" => $points,
                            ]);
                    $this->give_points($player_id, $points, true);
                }
                break;
            case "Eccesso": //most leftover blocks  6 / 3
            
                //$first = 0;
                $first_count = 0;
                //$second = 0;
                $second_count = 0;
                $blocks = [];
                foreach ($this->getAllDatas()['players'] as $player_id => $player) {
                    $blocks[$player_id] = (int)$player["blocks"];
                }
                if (count($blocks) == 1){
                    $blocks[0] = (int)$this->getObjectListFromDb("SELECT `blocks` FROM `otto`", true)[0];
                }
                foreach ($blocks as $player_id => $block_count) {
                    if ($block_count > 0){
                        if ($block_count > $first_count){ //put first place as first and replace second
                            $second_count = $first_count;
                            //$second = $first;
                            $first_count = $block_count;
                            //$first = $player_id;
                        } else if ($block_count > $second_count){ //put second place second
                            $second_count = $block_count;
                            //$second = $player_id;
                        }
                    }
                }
                $first = [];
                $second = [];
                foreach ($blocks as $player_id => $block_count) { //find ties for first
                        //double back to award points for ties
                        
                        if ($block_count == $first_count && $first_count > 0){
                           array_push($first, $player_id);
                        }

                        if ($block_count == $second_count && $second_count > 0 && $second_count < $first_count){
                           array_push($second, $player_id);
                        }
                        
                }
                $award_second = true;
                if (count($first) > 1 && $first_count > 0){ //split points for first tie
                    $points = (int) (9 / count($first));
                    foreach ($first as $player_id) {
                        $player_name = '';
                        if ((int)$player_id == 0){
                            $player_name = $this->ottoName();
                        } else {
                            $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                        }
                        $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for Eccesso'), [
                            "player_id" => $player_id,
                            "player_name" => $player_name,
                            "points" => $points,
                        ]);
                        $this->give_points($player_id, $points, true);
                        $award_second = false;
                    }
                } else { //reward first points if no tie exists
                    if ($first_count > 0){
                        $player_id = $first[0];
                        $player_name = '';
                        if ((int)$player_id == 0){
                            $player_name = $this->ottoName();
                        } else {
                            $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                        }
                        $this->notify->all("goalPoints", clienttranslate('${player_name} awarded 6 points for Eccesso'), [
                            "player_id" => $player_id,
                            "player_name" => $player_name,
                        ]);
                        $this->give_points($player_id, 6, true);
                    }
                }
                if ($award_second){
                    if (count($second) > 1 && $second_count > 0){ //split points for second tie
                        $points = (int) (3 / count($second));
                        foreach ($second as $player_id) {
                            $player_name = '';
                            if ((int)$player_id == 0){
                                $player_name = $this->ottoName();
                            } else {
                                $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                            }
                            $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for Eccesso'), [
                                "player_id" => $player_id,
                                "player_name" => $player_name,
                                "points" => $points,
                            ]);
                            $this->give_points($player_id, $points, true);
                        }
                    } else { //award points regularly otherwise
                        
                        if ($second_count > 0 && $second_count != $first_count){
                            $player_id = $second[0];
                            $player_name = '';
                            if ((int)$player_id == 0){
                                $player_name = $this->ottoName();
                            } else {
                                $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                            }
                            $this->notify->all("goalPoints", clienttranslate('${player_name} awarded 3 points for Eccesso'), [
                                "player_id" => $player_id,
                                "player_name" => $player_name,
                            ]);
                            $this->give_points($player_id, 3, true);
                        }
                    }
                }
                break;

            case "Dietro":   //most buildings on last 2 rows
                if ($row_size == 5){
                    $index = 34;
                } else {
                    $index = 27;
                }
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` > '$index'", true);
                $heights = [];
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` > '$index' AND `player` = '$player_id'", true);
                }
                $this->compare($buildings, $heights, $goal_name);
                break;
                

            case "Grande":   //most buildings above regulation height
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `height` > `max_height`", true);
                $heights = [];
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `height` > `max_height` AND `player` = '$player_id'", true);
                }
                $this->compare($buildings, $heights, $goal_name);
                break;
                
            case "Perfetto":   //2 points per building at regulation height
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `height` = `max_height` AND `player` = '$player_id'", true);
                    $points = count($heights[$player_id]) * 2;
                    $player_name = '';
                    if ((int)$player_id == 0){
                        $player_name = $this->ottoName();
                    } else {
                        $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                    }
                    if ($points > 0){
                        $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for Perfetto'), [
                                "player_id" => $player_id,
                                "player_name" => $player_name,
                                "points" => $points,
                            ]);
                        $this->give_points($player_id, $points, true);
                    }
                }
                
                break;

            case "Mezzo":   //most buildings on inner columns 6 / 3
                if ($row_size == 4){ //mezzo
                    $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` % 4 = 1 OR `lot_id` % 4 = 2", true);
                    $heights = [];
                    $player_ids = array_unique($buildings);
                    foreach ($player_ids as $player_id) {
                        $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE (`lot_id` % 4 = 1 OR `lot_id` % 4 = 2) AND `player` = '$player_id'", true);
                    }
                    $this->compare($buildings, $heights, $goal_name);
                } else { //centro
                    $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building` WHERE `lot_id` > 24 AND `lot_id` < 40 AND `lot_id` % 5 > 0 AND `lot_id` % 5 < 4", true);
                    $heights = [];
                    $player_ids = array_unique($buildings);
                    foreach ($player_ids as $player_id) {
                        $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` > 24 AND `lot_id` < 40 AND `lot_id` % 5 > 0 AND `lot_id` % 5 < 4 AND `player` = '$player_id'", true);
                    }
                    $this->compare($buildings, $heights, 'Centro');
                }
                break;
            case "Gruppo": //largest group of buildings
                $buildings = [];
                $heights = [];
                $temp_buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($temp_buildings);
                foreach ($player_ids as $player_id) {
                    $group = [];
                    $player_buildings =  $this->getObjectListFromDb("SELECT `lot_id` FROM `building` WHERE `player` = '$player_id'", true);
                    foreach($player_buildings as $id){
                        $temp_group = $this->check_group((int)$id, [(int)$id]);
                        if (count($temp_group) > count($group)){
                            $group = $temp_group;
                        } else if (count($temp_group) == count($group) && array_sum($this->get_group_heights($temp_group)) > array_sum($this->get_group_heights($group))){
                            $group = $temp_group;
                        }
                    }
                    foreach($group as $id){
                        array_push($buildings, $player_id);
                    }
                    $heights[$player_id] = $this->get_group_heights($group);
                }
                $this->compare($buildings, $heights, $goal_name);
                break;

            case "Equilibrato": //earn 5 points for having one of each roof type
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $buildings =  $this->getObjectListFromDb("SELECT `quality` FROM `building` WHERE `player` = '$player_id'", true);
                    if (count(array_unique($buildings)) > 2){
                        $player_name = '';
                        if ((int)$player_id == 0){
                            $player_name = $this->ottoName();
                        } else {
                            $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                        }
                        $this->notify->all("goalPoints", clienttranslate('${player_name} awarded 5 points for Equilibrato'), [
                                "player_id" => $player_id,
                                "player_name" => $player_name,
                            ]);
                        $this->give_points($player_id, 5, true);
                    }
                }
                break;

            case "Colonna": //most buildings in a single column
                $heights = [];
                $buildings = [];
                $temp_buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($temp_buildings);
                foreach ($player_ids as $player_id) {
                    $column_heights = [];
                    $col_count = 0;
                    for ($i = 0; $i < $row_size; $i++){
                        $column_heights = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `lot_id` % '$row_size' = '$i' AND `player` = '$player_id'", true);
                        if (count($column_heights) > $col_count){ //set heights if col count > previous
                            $heights[$player_id] = $column_heights;
                            $col_count = count($column_heights);
                        } else if (count($column_heights) == $col_count && $col_count > 0){ //break ties with height if equal
                            $col_count = count($column_heights);
                            if (array_key_exists($player_id, $heights)){
                                if (array_sum($column_heights) > array_sum($heights[$player_id])){
                                    $heights[$player_id] = $column_heights;
                                }
                            } else {
                                $heights[$player_id] = $column_heights;
                            }
                            
                        }
                    }
                    for ($i = 0; $i < count($heights[$player_id]); $i++){
                        array_push($buildings, $player_id);
                    }
                }
                $this->compare($buildings, $heights, $goal_name);
                break;

            case "Piccolo":   //3 points per building under regulation height
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `height` < `max_height` AND `player` = '$player_id'", true);
                    $points = count($heights[$player_id]) * 3;
                    if ($points > 0){
                        $player_name = '';
                        if ((int)$player_id == 0){
                            $player_name = $this->ottoName();
                        } else {
                            $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                        }
                        $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for Piccolo'), [
                                    "player_id" => $player_id,
                                    "player_name" => $player_name,
                                    "points" => $points,
                                ]);
                        $this->give_points($player_id, $points, true);
                    }
                }
                break;

            case "Altezza":   //2 points per building of unique height
                $buildings = $this->getObjectListFromDb("SELECT `player` FROM `building`", true);
                $player_ids = array_unique($buildings);
                foreach ($player_ids as $player_id) {
                    $heights[$player_id] = $this->getObjectListFromDb("SELECT `height` FROM `building` WHERE `player` = '$player_id'", true);
                    $unique_heights = array_unique($heights[$player_id]);
                    $points = count($unique_heights) * 2;
                    if ($points > 0){
                        $player_name = '';
                            if ((int)$player_id == 0){
                                $player_name = $this->ottoName();
                            } else {
                                $player_name = $this->getObjectListFromDb("SELECT `player_name` FROM `player` WHERE `player_id` = '$player_id'", true)[0];
                            }
                        $this->notify->all("goalPoints", clienttranslate('${player_name} awarded ${points} points for Altezza'), [
                                    "player_id" => $player_id,
                                    "player_name" => $player_name,
                                    "points" => $points,
                                ]);
                        $this->give_points($player_id, $points, true);
                    }
                }
                break;
        }
    }

    public function getEndScores(): array {
        $scores = [];
        foreach ($this->getAllDatas()['players'] as $player_id => $player) {
            $scores[$player_id]['building-points'] = $this->getStat('building_points', $player_id);
            $scores[$player_id]['gelato-points'] = $this->getStat('gelato_points', $player_id);
            $scores[$player_id]['goal-points'] = $this->getStat('goal_points', $player_id);
        }
        return $scores;
    }

    

    /**
     * Migrate database.
     *
     * You don't have to care about this until your game has been published on BGA. Once your game is on BGA, this
     * method is called everytime the system detects a game running with your old database scheme. In this case, if you
     * change your database scheme, you just have to apply the needed changes in order to update the game database and
     * allow the game to continue to run with your new version.
     *
     * @param int $from_version
     * @return void
     */
    public function upgradeTableDb($from_version)
    {
//       if ($from_version <= 1404301345)
//       {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
//
//       if ($from_version <= 1405061421)
//       {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
    }

    /*
     * Gather all information about current game situation (visible by the current player).
     *
     * The method is called each time the game interface is displayed to a player, i.e.:
     *
     * - when the game starts
     * - when a player refreshes the game page (F5)
     */
    protected function getAllDatas(): array
    {
        $result = [];

        // WARNING: We must only return information visible by the current player.
        $current_player_id = (int) $this->getCurrentPlayerId();
        $result["player_id"] = $current_player_id;
        // Get information about players.
        // NOTE: you can retrieve some extra field you added for "player" table in `dbmodel.sql` if you need it.
        $result["players"] = $this->getCollectionFromDb(
            "SELECT `player_id` `id`, `player_name` `name`, `player_score` `score`, `points`, `building_points`, `blocks`, `block_supply`, `real_color` `color`, `quality_pick`, `quality_id`, `lot_pick`, `bid_set` FROM `player`"
        );

        $result["otto"] = $this->getCollectionFromDb(
            "SELECT `score`, `points`, `building_points`, `blocks`, `block_supply`, `color`, `quality_pick`, `quality_id`, `lot_pick`, `bid_set` FROM `otto`"
        );

        $result["notifications"] = $this->getObjectListFromDb(
            "SELECT `player`, `notification` FROM `notifications`"
        );

        $result["bids"] = $this->getObjectListFromDb(
            "SELECT `player`, `lot_bid`, `block_bid`, `quality_bid`, `color`, `num_played`, `bid_id` FROM `bids`"
        );

        /*$result["orders"] = [];
        if (count($result["bids"]) == count($result["players"])){ //give player orders for properly displaying tiebreakers
            $result["orders"][0] = $this->playerOrder(0);
            $result["orders"][1] = $this->playerOrder(1);
            $result["orders"][2] = $this->playerOrder(2);
        }*/

        $result["bid_types"] = self::$BID_TYPES;
        $result["block_types"] = self::$BLOCK_TYPES;
        $result["quality_types"] = self::$QUALITY_TYPES;
        $result["block_types_bb"] = self::$BLOCK_TYPES_BB;
        $result["quality_types_bb"] = self::$QUALITY_TYPES_BB;

        $result['hand'] = $this->cards->getCardsInLocation( $current_player_id );


        $result["lots"] = $this->cards->getCardsInLocation( 'lot_stock' );

        $result["lot_deck"] = $this->cards->getCardsInLocation( 'lot_deck' );

        $result["blocks"] = $this->cards->getCardsInLocation( 'block_stock' );


        $result["quality"] = $this->cards->getCardsInLocation( 'quality_stock' );


        $result["goals"] = $this->cards->getCardsInLocation( 'goal_stock' );


        $result["buildings"] = $this->getObjectListFromDb(
            "SELECT `lot_id`, `player`, `height`, `max_height`, `quality`, `color`, `points` FROM `building`"
        );

        $result["removed"] = $this->getObjectListFromDb(
            "SELECT `lot_id` FROM `removed`", true
        );
        
        $result["goal_set"] = $this->tableOptions->get(100);
        $result["board_option"] = $this->tableOptions->get(102);

        return $result;
    }

    

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = [])
    {
        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        //TODO match player color to building color
        $gameinfos = $this->getGameinfos();
        $colors = ['blue', 'green', 'red', 'yellow', 'orange', 'purple'];
        $real_colors = ['blue', 'green', 'red', 'yellow'];
        $query_values = [];
        $goal_set = $this->tableOptions->get(100);

        foreach ($players as $player_id => $player) {
            // Now you can access both $player_id and $player array
            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                array_shift($colors),
                $player["player_canal"],
                addslashes($player["player_name"]),
                addslashes($player["player_avatar"]),
                array_shift($colors),
            ]);
        }

        $bb = $this->tableOptions->get(102);
        if (count($players) > 4 || $bb == 2){
            $this->setGameStateInitialValue('big_board', 1);
            //self::$LOT_TYPES = self::$LOT_TYPES_BB;
        } else {
            $this->setGameStateInitialValue('big_board', 0);
        }

        // Create players based on generic information.
        //
        // NOTE: You can add extra field on player table in the database (see dbmodel.sql) and initialize
        // additional fields directly here.
        static::DbQuery(
            sprintf(
                "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, real_color) VALUES %s",
                implode(",", $query_values)
            )
        );

        if ($this->getGameStateValue('big_board') == '1'){ //blue, green, red, yellow, orange, purple
            $colors = ['5391ae', '65a668', 'c25151', 'e2c742', 'e1973f', '855fa3'];
        } else {
            $colors = ['5391ae', '65a668', 'c25151', 'e2c742'];
        }
        
        

        
        

        $this->reattributeColorsBasedOnPreferences($players, $colors);
        $this->reloadPlayersBasicInfos();

        if (count($players) == 1){ //if player is solo, add otto to database
            self::$SOLO = true;
            $query_values = [];
            $query_values[] = vsprintf("('%s')", [
                'Otto Amalfi'
            ]);
            static::DbQuery(
            sprintf(
                "INSERT INTO otto (player_name) VALUES %s",
                implode(",", $query_values)
            )
        );

         }
        

        foreach ($players as $player_id => $player) { //match building color to player color
            switch( $this->getObjectListFromDb("SELECT `player_color` FROM `player` WHERE `player_id` = '$player_id'", true)[0] ){
                case '5391ae':
                    static::DbQuery("UPDATE `player` SET `real_color` = 'blue' WHERE `player_id` = '$player_id'");
                    static::DbQuery("UPDATE `otto` SET `color` = 'green'");
                    break;
                case '65a668':
                    static::DbQuery("UPDATE `player` SET `real_color` = 'green' WHERE `player_id` = '$player_id'");
                    static::DbQuery("UPDATE `otto` SET `color` = 'red'");
                    break;
                case 'c25151':
                    static::DbQuery("UPDATE `player` SET `real_color` = 'red' WHERE `player_id` = '$player_id'");
                    static::DbQuery("UPDATE `otto` SET `color` = 'yellow'");
                    break;
                case 'e2c742':
                    static::DbQuery("UPDATE `player` SET `real_color` = 'yellow' WHERE `player_id` = '$player_id'");
                    static::DbQuery("UPDATE `otto` SET `color` = 'blue'");
                    break;
                case 'e1973f':
                    static::DbQuery("UPDATE `player` SET `real_color` = 'orange' WHERE `player_id` = '$player_id'");
                    break;
                case '855fa3':
                    static::DbQuery("UPDATE `player` SET `real_color` = 'purple' WHERE `player_id` = '$player_id'");
                    break;

            }
        }

        $bid_sets = [1,2,3,4,5,6];
        $temp_id = 0;
        //create bids and set bidset
        foreach ($players as $player_id => $player) {
            $temp_id = $player_id;
            $cards = [];
            for ( $i = 1; $i < 14; $i++ ){
                $cards[] = ['type' => 'bid', 'type_arg' => $i, 'nbr' => 1 ];
            }
            $this->cards->createCards( $cards, $player_id);

            $bid = array_rand($bid_sets);
            $bid_set = $bid_sets[$bid];
            array_splice($bid_sets, $bid, 1);
            static::DbQuery("UPDATE `player` SET `bid_set` = '$bid_set' WHERE `player_id` = '$player_id'");
        }

        if (self::$SOLO){ //add cards for otto
            $player_id = 0;
            $cards = [];
            for ( $i = 1; $i < 14; $i++ ){
                $cards[] = ['type' => 'bid', 'type_arg' => $i, 'nbr' => 1 ];
            }
            $this->cards->createCards( $cards, 0);

            $bid = array_rand($bid_sets);
            $bid_set = $bid_sets[$bid];
            array_splice($bid_sets, $bid, 1);
            static::DbQuery("UPDATE `otto` SET `bid_set` = '$bid_set'");
        }

        
        if ($this->getGameStateValue('big_board') == '1'){ //create cards for big board
            $cards = [];
            
            for ( $i = 20; $i < 45; $i++ ){//create lot tiles
                $cards[] = ['type' => 'lot', 'type_arg' => $i, 'nbr' => 1 ];
            }
            $this->cards->createCards( $cards, 'lot_deck' );

            //create block tiles
            $cards = [];
            
            for ( $i = 40; $i < 65; $i++ ){
                $cards[] = ['type' => 'blocks', 'type_arg' => $i, 'nbr' => 1 ];
            }
            //array_reverse($cards);
            for ($i = 0; $i < count($cards); $i++ ){
                $this->cards->createCards( [$cards[$i]], 'block_deck' );
            }

            //create quality tiles
            $cards = [];
            
            for ( $i = 60; $i < 85; $i++ ){
                $cards[] = ['type' => 'quality', 'type_arg' => $i, 'nbr' => 1 ];
            }
            //array_reverse($cards);
            for ($i = 0; $i < count($cards); $i++ ){
                $this->cards->createCards( [$cards[$i]], 'quality_deck' );
            }

        } else {

            //create cards for standard board
            $cards = [];
            
            for ( $i = 20; $i < 36; $i++ ){//create lot tiles
                $cards[] = ['type' => 'lot', 'type_arg' => $i, 'nbr' => 1 ];
            }
            $this->cards->createCards( $cards, 'lot_deck' );

            //create block tiles
            $cards = [];
            
            for ( $i = 40; $i < 56; $i++ ){
                $cards[] = ['type' => 'blocks', 'type_arg' => $i, 'nbr' => 1 ];
            }
            //array_reverse($cards);
            for ($i = 0; $i < count($cards); $i++ ){
                $this->cards->createCards( [$cards[$i]], 'block_deck' );
            }

            //create quality tiles
            $cards = [];
            
            for ( $i = 60; $i < 76; $i++ ){
                $cards[] = ['type' => 'quality', 'type_arg' => $i, 'nbr' => 1 ];
            }
            //array_reverse($cards);
            for ($i = 0; $i < count($cards); $i++ ){
                $this->cards->createCards( [$cards[$i]], 'quality_deck' );
            }
        }

        //create goal cards
        $cards = [];
        
        switch($goal_set){
            case 1: //random goal set
                for ( $i = 80; $i < 86; $i++ ){ //add 6 if card is on back side
                    if (mt_rand(1, 100) < 50){
                        $cards[] = ['type' => 'goal', 'type_arg' => $i, 'nbr' => 1 ];
                    } else {
                        $cards[] = ['type' => 'goal', 'type_arg' => $i+6, 'nbr' => 1 ];
                    }
                    
                }
                break;
            case 2: //Big in the Back: Grande, Gruppo, Dietro
                $cards[] = ['type' => 'goal', 'type_arg' => 80, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 87, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 83, 'nbr' => 1 ];
                break;
            case 3: //The Spice of Life: Altezza, Equilibrato, Bordo
                $cards[] = ['type' => 'goal', 'type_arg' => 91, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 88, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 84, 'nbr' => 1 ];
                break;
            case 4: //Tiny Homes: Piccolo, Solitario, Eccesso
                $cards[] = ['type' => 'goal', 'type_arg' => 86, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 81, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 82, 'nbr' => 1 ];
                break;
            case 5: //Balanced and Centered: Mezzo, Perfetto, Colonna
                $cards[] = ['type' => 'goal', 'type_arg' => 90, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 85, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 89, 'nbr' => 1 ];
                break;
            case 6: //Location, Location, Location: Solitario, Mezzo, Dietro
                $cards[] = ['type' => 'goal', 'type_arg' => 81, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 90, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 83, 'nbr' => 1 ];
                break;
            case 7: //More Blocks, More Problems: Grande, Altezza, Eccesso
                $cards[] = ['type' => 'goal', 'type_arg' => 80, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 91, 'nbr' => 1 ];
                $cards[] = ['type' => 'goal', 'type_arg' => 82, 'nbr' => 1 ];
                break;
            default: //random goal set
                for ( $i = 80; $i < 86; $i++ ){ //add 6 if card is on back side
                    if (mt_rand(1, 100) < 50){
                        $cards[] = ['type' => 'goal', 'type_arg' => $i, 'nbr' => 1 ];
                    } else {
                        $cards[] = ['type' => 'goal', 'type_arg' => $i+6, 'nbr' => 1 ];
                    }
                    
                }
                break;
        }
        
        
        //array_reverse($cards);
        $this->cards->createCards( $cards, 'goal_deck' );


        //remove cards for 2 or 3 player game
        $this->cards->shuffle('lot_deck');
        $removed = [];
        switch(count($players)) {
            case 3:
                $removed = $this->cards->pickCardsForLocation(1, 'lot_deck', 'discard', 0, false );
                break;
            case 2:
                $removed = $this->cards->pickCardsForLocation(4, 'lot_deck', 'discard', 0, false );
                break;
            case 1:
                $removed = $this->cards->pickCardsForLocation(4, 'lot_deck', 'discard', 0, false );
                break;
            case 4:
                if ($bb == 2){
                    $removed = $this->cards->pickCardsForLocation(1, 'lot_deck', 'discard', 0, false );
                }
                break;
            case 6:
                $removed = $this->cards->pickCardsForLocation(1, 'lot_deck', 'discard', 0, false );
                break;
        }
        for ( $i = 0; $i < count($removed); $i++ ){
            $id = $removed[$i]['type_arg'];
            $lot_name = $this->get_lot($id);
            $this->notify->all('lotRemoved', clienttranslate('Discarded lot ' . $lot_name), [
                "lot_id" => $id,
            ]);
            static::DbQuery(
            sprintf(
                "INSERT INTO removed (lot_id) VALUES %s",
                "(" . $id . ")",
            )
        );

        }
        
        //stock initial tiles
        $this->stock_stocks(count($players), $players);

        $this->cards->shuffle('block_deck');
        $this->cards->shuffle('quality_deck');
        if ($goal_set == 1){
            $this->cards->shuffle('goal_deck');
        }
        
        $goal_stock = $this->cards->pickCardsForLocation(3, 'goal_deck', 'goal_stock', 0, false );
        // Init global values with their initial values.
        
        
        $this->setGameStateInitialValue("prev_state", 0);
        $this->setGameStateInitialValue("current_player", 0);
        $this->setGameStateInitialValue("player_lot", 0);
        $this->setGameStateInitialValue("player_bids", []);

        // Init game statistics.
        //
        // NOTE: statistics used in this file must be defined in your `stats.inc.php` file.

        // Dummy content.
        $this->initStat("table", "turns_number", 0);
        $this->initStat("player", "building_points", 0);
        $this->initStat("player", "gelato_points", 0);
        $this->initStat("player", "goal_points", 0);

        // TODO: Setup the initial game situation here.

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();
        
        
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: pass).
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, otherwise it will fail with a
     * "Not logged" error message.
     *
     * @param array{ type: string, name: string } $state
     * @param int $active_player
     * @return void
     * @throws feException if the zombie mode is not supported at this game state.
     */
    protected function zombieTurn(array $state, int $active_player): void
    {
        $state_name = $state["name"];

        if ($state["type"] === "activeplayer") {
            switch ($state_name) {
                default:
                {
                    $this->gamestate->nextState("zombiePass");
                    break;
                }
            }

            return;
        }

        // Make sure player is in a non-blocking status for role turn.
        if ($state["type"] === "multipleactiveplayer") {
            $this->gamestate->setPlayerNonMultiactive($active_player, '');
            return;
        }

        throw new \feException("Zombie mode not supported at this game state: \"{$state_name}\".");
    }
}
