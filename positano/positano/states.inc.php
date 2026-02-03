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
 * states.inc.php
 *
 * Positano game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: $this->checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!


$machinestates = [

    // The initial state. Please do not modify.

    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["" => 2]
    ),

    // Note: ID=2 => your first state

    2 => [
        "name" => "playerBid",
        "description" => clienttranslate('Other players must place a bid'),
        "descriptionmyturn" => clienttranslate('${you} must place a bid'),
        "type" => "multipleactiveplayer",
        "action"=> "stMultiPlayerInit",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actPlayBid", 
            "actOttoBid",
        ],
        "transitions" => ["playBid" => 7, "ottoPlayBid" => 2]
    ],

    3 => [
        "name" => "playerPickLot",
        "description" => clienttranslate('${actplayer} must choose a lot'),
        "descriptionmyturn" => clienttranslate('${you} must choose a lot'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actPickLot", 
        ],
        "transitions" => ["pickLot" => 7]
    ],

    13 => [
        "name" => "ottoPickLot",
        "description" => clienttranslate('${actplayer} must choose a lot'),
        "descriptionmyturn" => clienttranslate('Otto must choose a lot'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoPickLot", 
        ],
        "transitions" => ["pickLot" => 7]
    ],

    4 => [
        "name" => "playerPickBlocks",
        "description" => clienttranslate('${actplayer} must pick blocks'),
        "descriptionmyturn" => clienttranslate('${you} must pick blocks'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actPickBlocks", 
        ],
        "transitions" => ["pickBlocks" => 7]
    ],

    14 => [
        "name" => "ottoPickBlocks",
        "description" => clienttranslate('${actplayer} must pick blocks'),
        "descriptionmyturn" => clienttranslate('Otto must pick blocks'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoPickBlocks", 
        ],
        "transitions" => ["pickBlocks" => 7]
    ],

    5 => [
        "name" => "playerPickQuality",
        "description" => clienttranslate('${actplayer} must choose a quality'),
        "descriptionmyturn" => clienttranslate('${you} must choose a quality'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actPickQuality", 
        ],
        "transitions" => ["pickQuality" => 6]
    ],

    15 => [
        "name" => "ottoPickQuality",
        "description" => clienttranslate('${actplayer} must choose a quality'),
        "descriptionmyturn" => clienttranslate('Otto must choose a quality'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoPickQuality", 
        ],
        "transitions" => ["ottoPickQuality" => 10]
    ],

    16 => [
        "name" => "ottoBid",
        "description" => clienttranslate('${actplayer} must choose a quality'),
        "descriptionmyturn" => clienttranslate('Otto must play a bid'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoBid", 
        ],
        "transitions" => ["ottoPlayBid" => 2]
    ],

    6 => [
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must construct a building'),
        "descriptionmyturn" => clienttranslate('${you} must construct a building; Height: '),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actBuild", 
            "actPass",
        ],
        "transitions" => ["build" => 7, "addOn" => 8, "remodel" => 9, "cantPlay" => 7]
    ],

    10 => [
        "name" => "ottoTurn",
        "description" => clienttranslate('${actplayer} must construct a building'),
        "descriptionmyturn" => clienttranslate('Otto must construct a building; Height: '),
        "type" => "activeplayer",
        "args" => "argOttoTurn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoBuild", 
            "actPass",
        ],
        "transitions" => ["build" => 7, "addOn" => 11, "remodel" => 12, "cantPlay" => 7]
    ],

    11 => [
        "name" => "ottoAddOn",
        "description" => clienttranslate('${actplayer} may add one block to a building'),
        "descriptionmyturn" => clienttranslate('Otto may add one block to a building'),
        "type" => "activeplayer",
        "args" => "argOttoAddOn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoAddOn",
            "actPass",
        ],
        "transitions" => ["addOn" => 7, "cantPlay" => 7]
    ],

    12 => [
        "name" => "ottoRemodel",
        "description" => clienttranslate('${actplayer} may remodel a building'),
        "descriptionmyturn" => clienttranslate('Otto may select a building to remodel'),
        "type" => "activeplayer",
        "args" => "argOttoRemodel",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actOttoRemodel",
            "actPass",
        ],
        "transitions" => ["remodel" => 7, "cantPlay" => 7]
    ],

    8 => [
        "name" => "playerAddOn",
        "description" => clienttranslate('${actplayer} may add one block to a building'),
        "descriptionmyturn" => clienttranslate('${you} may add one block to a building'),
        "type" => "activeplayer",
        "args" => "argAddOn",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actAddOn",
            "actPass",
        ],
        "transitions" => ["addOn" => 7, "cantPlay" => 7]
    ],

    9 => [
        "name" => "playerRemodel",
        "description" => clienttranslate('${actplayer} may remodel a building'),
        "descriptionmyturn" => clienttranslate('${you} may select a building to remodel'),
        "type" => "activeplayer",
        "args" => "argRemodel",
        "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            "actRemodel",
            "actPass",
        ],
        "transitions" => ["remodel" => 7, "cantPlay" => 7]
    ],

    7 => [
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,
        "transitions" => ["endGame" => 99, "nextPlayer" => 6, "nextLot" => 3, "nextBlock" => 4,"nextQuality"=> 5, "nextBid" => 2, "ottoLot" => 13, "ottoBlock" => 14, "ottoQuality" => 15, "ottoBid" => 16]
    ],

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],

];



