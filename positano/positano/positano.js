/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Positano implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * positano.js
 *
 * Positano user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
        "dojo", "dojo/_base/declare",
        "ebg/core/gamegui",
        "ebg/counter",
        "ebg/stock"
    ],
    function(dojo, declare) {
        return declare("bgagame.positano", ebg.core.gamegui, {
            constructor: function() {
                console.log('positano constructor');

                // Here, you can init the global variables of your user interface
                // Example:
                this.removed_lots = [];


            },

            /*
                setup:
                
                This method must set up the game user interface according to current game situation specified
                in parameters.
                
                The method is called each time the game interface is displayed to a player, ie:
                _ when the game starts
                _ when a player refreshes the game page (F5)
                
                "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
            */

            setup: function(gamedatas) {
                console.log("Starting game setup");

                // Example to add a div on the game area
                document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="player-tables"></div>
            `);
                this.big_board = false;
                this.playerCount = Object.values(gamedatas.players).length;
                if (this.playerCount > 4) {
                    this.big_board = true;
                }
                this.board_option = parseInt(gamedatas.board_option);

                if (this.big_board) { //add big board
                    this.getGameAreaElement().insertAdjacentHTML('beforeend', `
                    
                    <div id="entire-board-container">
                    <div id="big-game-board-container">
                        
                        <div id="front-view-big-board">
                            
                        </div>
                    </div>
                    <div id="myhand_wrap" class="whiteblock">
                        <b id="myhand_label">${_('My hand')}</b>
                        <div id="myhand">
                            <div class="playertablecard"></div>
                        </div>
                    </div>
                    
                    <div id="big-bid-board-container">
                        <div id="big-beach-board-container">
                            <div id="big-beach-board">
                                <div id="lots">
                                
                                </div>
                                <div id="blocks">
                                
                                </div>
                                <div id="quality">
                                
                                </div>
                            </div>
                        </div>
                         <div id="big-bid-display" class="whiteblock">
                            <h3 id="bid-order-title">Bid Order</h3>
                            <div id="lotbids" class="bigbidformat">
                            
                            </div>
                            <div id="blockbids" class="bigbidformat">
                            
                            </div>
                            <div id="qualitybids" class="bigbidformat">
                            
                            </div>
                        </div>
                    </div>
                   
                    <div id="goals" class="goalstock">
                        <div class="playertablecard"></div>
                    </div>
                    
                    </div>
                    
                `);
                } else if (this.board_option == 2) {
                    this.getGameAreaElement().insertAdjacentHTML('beforeend', `
                    
                    <div id="entire-board-container">
                    <div id="big-game-board-container">
                        
                        <div id="front-view-big-board">
                            
                        </div>
                    </div>
                    
                    <div id="myhand_wrap" class="whiteblock">
                        <b id="myhand_label">${_('My hand')}</b>
                        <div id="myhand">
                            <div class="playertablecard"></div>
                        </div>
                    </div>
                    <div id="bid-board-container">
                        <div id="beach-board">
                            <div id="lots">
                            
                            </div>
                            <div id="blocks">
                            
                            </div>
                            <div id="quality">
                            
                            </div>
                        </div>
                        <div id="bid-display" class="whiteblock">
                            <h3 id="bid-order-title">Bid Order</h3>
                            <div id="lotbids" class="bidformat">
                            
                            </div>
                            <div id="blockbids" class="bidformat">
                            
                            </div>
                            <div id="qualitybids" class="bidformat">
                            
                            </div>
                        </div>
                    </div>
                    <div id="goals" class="goalstock">
                        <div class="playertablecard"></div>
                    </div>
                    
                    
                   </div>
                `);
                    this.big_board = true;
                } else {
                    //add board to game
                    this.getGameAreaElement().insertAdjacentHTML('beforeend', `
                    
                    <div id="entire-board-container">
                    <div id="game-board-container">
                        
                        <div id="front-view">
                            
                        </div>
                    </div>
                    
                    <div id="myhand_wrap" class="whiteblock">
                        <b id="myhand_label">${_('My hand')}</b>
                        <div id="myhand">
                            <div class="playertablecard"></div>
                        </div>
                    </div>
                    <div id="bid-board-container">
                        <div id="beach-board">
                            <div id="lots">
                            
                            </div>
                            <div id="blocks">
                            
                            </div>
                            <div id="quality">
                            
                            </div>
                        </div>
                        <div id="bid-display" class="whiteblock">
                            <h3 id="bid-order-title">Bid Order</h3>
                            <div id="lotbids" class="bidformat">
                            
                            </div>
                            <div id="blockbids" class="bidformat">
                            
                            </div>
                            <div id="qualitybids" class="bidformat">
                            
                            </div>
                        </div>
                    </div>
                    <div id="goals" class="goalstock">
                        <div class="playertablecard"></div>
                    </div>
                    
                    
                   </div>
                `);
                }



                // Setting up player boards
                this.playerPoints = 0;
                this.playerBlockCount = 1;
                this.chosenLot = 0;
                this.player_ids = [];
                this.player_colors = [];
                this.quality_picks = [];
                this.bid_sets = [];
                this.player_id = gamedatas.player_id;
                this.show_stars = true;
                this.buildings = gamedatas.buildings;

                //add switch panel for star toggle
                let panels = document.getElementById('right-side-first-part');
                panels.insertAdjacentHTML('beforeend', `
                    <div class="player-board">
                        Show stars
                        <label id="startoggle" class="switch">
                            <input type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>
                `);

                let starswitch = document.getElementById('startoggle');
                starswitch.addEventListener("click", (e) => {
                    if (this.show_stars) {
                        this.show_stars = false;
                        this.removeStars();
                    } else {
                        this.show_stars = true;
                        this.addStars(this.buildings);
                    }
                    //console.log(this.show_stars)
                });




                Object.values(gamedatas.players).forEach(player => {
                    this.playerBlockCount = player.blocks;
                    this.playerPoints = parseInt(player.building_points) + parseInt(player.points);
                    console.log('points: ' + player.building_points + player.points);
                    this.chosenLot = player.lot_pick;
                    this.player_ids.push(player.id);
                    this.player_colors[player.id] = player.color;
                    this.bid_sets[player.id] = player.bid_set;
                    this.quality_picks[player.id] = player.quality_id;
                    this.getPlayerPanelElement(player.id).insertAdjacentHTML('beforeend', `
                    <div id="player-counter-${player.id}">
                        <div id="block-counter-${player.id}">Blocks: ${this.playerBlockCount}</div>
                        <div id="supply-counter-${player.id}">Supply: ${player.block_supply}</div>
                    </div>
                `);

                    // example of adding a div for each player
                    /*document.getElementById('player-tables').insertAdjacentHTML('beforeend', `
                        <div id="player-table-${player.id}">
                            <strong>${player.name}</strong>
                            <div>Player zone content goes here</div>
                            <div id="point-counter-${player.id}">Points: ${this.playerPoints}</div>
                        </div>
                    `);*/
                });



                console.log(this.player_colors);

                this.solo = false;


                if (this.playerCount == 1) { //Add player panel for otto
                    this.solo = true;

                    Object.values(gamedatas.otto).forEach(otto => {
                        var color = '';
                        switch (otto.color) {
                            case 'blue':
                                color = '#5391ae';
                                break;
                            case 'green':
                                color = '#65a668';
                                break;
                            case 'red':
                                color = '#c25151';
                                break;
                            case 'yellow':
                                color = '#e2c742';
                                break;
                            case 'orange':
                                color = '#e1973f';
                                break;
                            case 'purple':
                                color = '#855fa3';
                                break;
                        }
                        this.addAutomataPlayerPanel(0, 'Otto Amalfi', {
                            iconClass: 'otto-avatar',
                            score: otto.score,
                            color: color,
                        });
                        this.bid_sets[0] = otto.bid_set;
                        console.log('added otto panel for score ' + otto.score);
                        this.getPlayerPanelElement(0).insertAdjacentHTML('beforeend', `
                            <div id="player-counter-0">
                                <div id="block-counter-0">Blocks: ${otto.blocks}</div>
                                <div id="supply-counter-0">Supply: ${otto.block_supply}</div>
                            </div>
                        `);
                    });
                }


                // game interface


                if (this.big_board) { //place squares on big board
                    /*const board = document.getElementById('big-board');
                    var hor_scale = 137.8;
                    var ver_scale = 120.5;
                    for (let x = 1; x <= 5; x++) {
                        for (let y = 1; y <= 5; y++) {
                            const left = Math.round((x - 1) * hor_scale + 24.5);
                            const top = Math.round((y - 1) * ver_scale + 17.7);
                            // we use afterbegin to make sure squares are placed before buildings
                            board.insertAdjacentHTML(`afterbegin`, `<div id="square_${x}_${6-y}" class="square" style="left: ${left}px; top: ${top}px;"></div>`);
                        }
                    }*/

                    //place squares on big front view
                    const front_view = document.getElementById('front-view-big-board');
                    hor_scale = 165.5;
                    ver_scale = 58;
                    for (let x = 1; x <= 5; x++) {
                        for (let y = 1; y <= 12; y++) {
                            const left = Math.round((x - 1) * hor_scale + 52);
                            const top = Math.round((y - 1) * ver_scale + 148);
                            // we use afterbegin to make sure squares are placed before buildings
                            front_view.insertAdjacentHTML(`afterbegin`, `<div id="view_marker_${x}_${13-y}" class="frontsquare" style="left: ${left}px; top: ${top}px;"></div>`);
                        }
                    }
                } else {

                    /*const board = document.getElementById('board'); //place squares on board
                    var hor_scale = 173.1;
                    var ver_scale = 129.5;
                    for (let x = 1; x <= 4; x++) {
                        for (let y = 1; y <= 4; y++) {
                            const left = Math.round((x - 1) * hor_scale + 40);
                            const top = Math.round((y - 1) * ver_scale + 15);
                            // we use afterbegin to make sure squares are placed before buildings
                            board.insertAdjacentHTML(`afterbegin`, `<div id="square_${x}_${5-y}" class="square" style="left: ${left}px; top: ${top}px;"></div>`);
                        }
                    }*/

                    //place squares on front view
                    const front_view = document.getElementById('front-view');
                    hor_scale = 168;
                    ver_scale = 60;
                    for (let x = 1; x <= 4; x++) {
                        for (let y = 1; y <= 9; y++) {
                            const left = Math.round((x - 1) * hor_scale + 48);
                            const top = Math.round((y - 1) * ver_scale + 205);
                            // we use afterbegin to make sure squares are placed before buildings
                            front_view.insertAdjacentHTML(`afterbegin`, `<div id="view_marker_${x}_${10-y}" class="frontsquare" style="left: ${left}px; top: ${top}px;"></div>`);
                        }
                    }
                }
                this.y_adjust = -83;
                this.x_adjust = 78;


                //insert buildings
                for (i in gamedatas.buildings) {
                    var b = gamedatas.buildings;
                    //this.placeBuilding(b[i]['player'], b[i]['lot_id'], b[i]['quality'], b[i]['height'], b[i]['color'], b[i]['points']);
                    this.placeFrontViewBuilding(b[i]['player'], b[i]['lot_id'], b[i]['quality'], b[i]['height'], b[i]['color'], b[i]['points']);
                    if (this.big_board && b[i]['lot_id'] > 34) {
                        document.getElementById('entire-board-container').style.transform = 'translate(0, 0)';
                    }
                    if (!this.big_board && b[i]['lot_id'] > 31) {
                        document.getElementById('entire-board-container').style.transform = 'translate(0, 0)';
                    }
                }

                // Create cards types:
                //this.cardwidth = 750;
                //this.cardheight = 1050;
                this.cardwidth = 200;
                this.cardheight = 279.27272727
                this.margin = 23;

                // Player hand
                this.playerHand = new ebg.stock(); // new stock object for hand
                this.playerHand.create(this, $('myhand'), this.cardwidth, this.cardheight);

                this.playerHand.image_items_per_row = 7; // 7 images per row


                this.current_id = gamedatas.player_id;
                var bid_set = this.bid_sets[this.current_id];
                console.log('setting bid set to ' + bid_set + ' for player ' + this.current_id);
                var row = (parseInt(bid_set) - 1) * 7;
                for (var value = 1; value <= 7; value++) {
                    // Build card type id
                    var card_type_id = value;

                    this.playerHand.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/AllCards_optimized.png', card_type_id - 1 + row);


                }
                for (var value = 1; value <= 6; value++) {
                    // Build card type id
                    var card_type_id = value + 7;
                    this.playerHand.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/AllCards_optimized.png', value + 41);

                }
                this.playerHand.resizeItems(100, 140, 100 * 7, 140 * 11);
                this.playerHand.setOverlap(90, 0);
                this.playerHand.setSelectionMode(0);
                this.playerHand.extraClasses = 'rounded_corners';




                // Lot stock
                if (this.big_board) {
                    this.cardwidth = 300;
                    this.cardheight = 300;
                    this.resize = 150;
                    this.lotStock = new ebg.stock(); // new stock object for hand
                    this.lotStock.create(this, $('lots'), this.cardwidth, this.cardheight);

                    this.lotStock.image_items_per_row = 5; // 13 images per row
                    this.lotStock.setSelectionMode(0);
                    for (var value = 0; value < 25; value++) {
                        // Build card type id
                        var card_type_id = value + 20;

                        this.lotStock.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/lot_sheet_expansion.png', value);

                        //this.playerHand.addToStockWithId(value, value);
                    }
                    this.lotStock.resizeItems(this.resize, this.resize, this.resize * 5, this.resize * 5);
                } else {
                    this.cardwidth = 300;
                    this.cardheight = 300;
                    this.resize = 150;
                    this.lotStock = new ebg.stock(); // new stock object for hand
                    this.lotStock.create(this, $('lots'), this.cardwidth, this.cardheight);

                    this.lotStock.image_items_per_row = 4; // 13 images per row
                    this.lotStock.setSelectionMode(0);
                    for (var value = 0; value < 16; value++) {
                        // Build card type id
                        var card_type_id = value + 20;

                        this.lotStock.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/lot_sheet_fixed.png', value);

                        //this.playerHand.addToStockWithId(value, value);
                    }
                    this.lotStock.resizeItems(this.resize, this.resize, this.resize * 4, this.resize * 4);
                }


                this.lotStock.item_margin = this.margin;
                this.lotStock.extraClasses = 'lotclass';
                dojo.connect(this.lotStock, 'onChangeSelection', this, 'onLotStockSelection');

                // Block stock
                this.blockStock = new ebg.stock(); // new stock object for hand
                this.cardwidth = 200;
                this.cardheight = 200;
                this.blockStock.create(this, $('blocks'), this.cardwidth, this.cardheight);

                if (this.big_board) { //big board blocks
                    this.blockStock.image_items_per_row = 5; // 5 images per row
                    this.blockStock.setSelectionMode(0);
                    for (var value = 0; value < 25; value++) {
                        // Build card type id
                        var card_type_id = value + 40;

                        this.blockStock.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/block_sheet_big.png', value);
                        //this.playerHand.addToStockWithId(value, value);
                    }
                    this.blockStock.resizeItems(this.resize, this.resize, this.resize * 5, this.resize * 5);

                } else { //regular board blocks
                    this.blockStock.image_items_per_row = 4; // 4 images per row
                    this.blockStock.setSelectionMode(0);
                    for (var value = 1; value <= 16; value++) {
                        // Build card type id
                        var card_type_id = value + 39;

                        this.blockStock.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/block_sheet.png', value - 1);
                        //this.playerHand.addToStockWithId(value, value);
                    }
                    this.blockStock.resizeItems(this.resize, this.resize, this.resize * 4, this.resize * 4);
                }
                this.blockStock.item_margin = this.margin;
                this.blockStock.extraClasses = 'blockclass';
                dojo.connect(this.blockStock, 'onChangeSelection', this, 'onBlockStockSelection');

                // Quality stock
                this.qualityStock = new ebg.stock(); // new stock object for hand
                this.qualityStock.create(this, $('quality'), this.cardwidth, this.cardheight);

                if (this.big_board) { //big board quality
                    this.qualityStock.image_items_per_row = 5; // 4 images per row
                    this.qualityStock.setSelectionMode(0);
                    for (var value = 0; value < 25; value++) {
                        // Build card type id
                        var card_type_id = value + 60;

                        this.qualityStock.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/quality_sheet_expansion.png', value);
                        //this.playerHand.addToStockWithId(value, value);
                    }
                    this.qualityStock.resizeItems(this.resize, this.resize, this.resize * 5, this.resize * 5);
                } else { //regular board quality
                    this.qualityStock.image_items_per_row = 4; // 4 images per row
                    this.qualityStock.setSelectionMode(0);
                    for (var value = 1; value <= 16; value++) {
                        // Build card type id
                        var card_type_id = value + 59;

                        this.qualityStock.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/quality_sheet.png', value - 1);
                        //this.playerHand.addToStockWithId(value, value);
                    }
                    this.qualityStock.resizeItems(this.resize, this.resize, this.resize * 4, this.resize * 4);
                }
                this.qualityStock.item_margin = this.margin;
                this.qualityStock.extraClasses = 'qualityclass';
                dojo.connect(this.qualityStock, 'onChangeSelection', this, 'onQualityStockSelection');

                //adding tooltips (they are ugly)
                //this.playerHand.onItemCreate = dojo.hitch(this, 'setupHandCard');
                //this.lotStock.onItemCreate = dojo.hitch(this, 'setupStockCard');
                //this.blockStock.onItemCreate = dojo.hitch(this, 'setupStockCard');
                //this.qualityStock.onItemCreate = dojo.hitch(this, 'setupStockCard');


                //Goal stock
                //this.cardwidth = 413;
                //this.cardheight = 562;
                console.log('goal set: ' + gamedatas.goal_set);
                this.cardwidth = 200;
                this.cardheight = 279.27272727
                this.goalStock = new ebg.stock(); // new stock object for hand
                this.goalStock.create(this, $('goals'), this.cardwidth, this.cardheight);

                this.goalStock.image_items_per_row = 7; // 7 images per row
                this.goalStock.setSelectionMode(0);
                var goal_num = 49;
                if (this.big_board) {
                    goal_num = 63;
                }

                for (var value = 0; value < 12; value++) {
                    // Build card type id
                    var goal = 0;

                    var card_type_id = value + 80;
                    if (value > 5) {
                        goal = value + 1;
                    } else {
                        goal = value;
                    }

                    //console.log('adding goal ' + card_type_id + ' img position ' + goal);

                    this.goalStock.addItemType(card_type_id, 1, g_gamethemeurl + 'img/AllCards_optimized.png', goal + goal_num);

                    //this.goalStock.addToStockWithId(card_type_id, card_type_id);
                }
                this.goalStock.resizeItems(150, 204, 150 * 7, 204 * 11);
                this.goalStock.extraClasses = 'rounded_corners';


                //bid order stocks
                this.cardwidth = 150; //single bid order stock
                this.cardheight = 20;
                let margin = 10;
                if (this.playerCount > 3) {
                    margin = 5;
                }
                if (this.playerCount > 4) {
                    this.cardwidth = 112; //single bid order stock
                    this.cardheight = 15;
                }

                /*this.cardwidth = 100;
                this.cardheight = 100;*/
                this.lotBidStock = new ebg.stock(); // new stock object for hand
                this.lotBidStock.create(this, $('lotbids'), this.cardwidth, this.cardheight);
                this.lotBidStock.setSelectionMode(0);
                if (this.playerCount > 4) {
                    console.log('setting stock to bigbidstock')
                    this.lotBidStock.extraClasses = 'bigbidstock';
                } else {
                    this.lotBidStock.extraClasses = 'bidstock';
                }
                this.lotBidStock.item_margin = margin;
                //this.lotBidStock.container_div.height = "150px";

                this.blockBidStock = new ebg.stock(); // new stock object for hand
                this.blockBidStock.create(this, $('blockbids'), this.cardwidth, this.cardheight);
                this.blockBidStock.setSelectionMode(0);
                if (this.playerCount > 4) {
                    this.blockBidStock.extraClasses = 'bigbidstock';
                } else {
                    this.blockBidStock.extraClasses = 'bidstock';
                }
                this.blockBidStock.item_margin = margin;
                //this.blockBidStock.container_div.height = "150px";

                this.qualityBidStock = new ebg.stock(); // new stock object for hand
                this.qualityBidStock.create(this, $('qualitybids'), this.cardwidth, this.cardheight);
                this.qualityBidStock.setSelectionMode(0);
                if (this.playerCount > 4) {
                    this.qualityBidStock.extraClasses = 'bigbidstock';
                } else {
                    this.qualityBidStock.extraClasses = 'bidstock';
                }
                this.qualityBidStock.item_margin = margin;
                //this.qualityBidStock.container_div.height = "150px";


                /*this.bidStock = new ebg.stock(); // new stock object for hand
                this.bidStock.create(this, $('bidtracker'), this.cardwidth, this.cardheight);
                this.bidStock.setSelectionMode(0);
                this.bidStock.extraClasses = 'singlebidstock';
                this.bidStock.item_margin = 20;*/

                for (var i = 10; i <= 70; i += 10) {
                    for (var b = 0; b < 6; b++) {
                        var booster = b;
                        if (b < 2) {
                            booster += 5;
                        } else if (b > 3) {
                            booster += 17;
                        } else {
                            booster += 11;
                        }
                        var id = i + booster;
                        //g_gamethemeurl + 'img/transparent-background.jpg'
                        //console.log('adding bid tracker ' + id);
                        //this.lotBidStock.addItemType(id, id, '', id);
                        //this.blockBidStock.addItemType(id, id, '', id);
                        //this.qualityBidStock.addItemType(id, id, '', id);
                        for (var p = 0; p < this.player_ids.length; p++) {
                            this.lotBidStock.addItemType(id, 100 - id, '', this.player_ids[p]);
                            this.blockBidStock.addItemType(id, 100 - id, '', this.player_ids[p]);
                            this.qualityBidStock.addItemType(id, 100 - id, '', this.player_ids[p]);
                            //this.bidStock.addItemType(id, 100 - id, '', this.player_ids[p]);
                        }

                    }
                }
                this.lotBidStock.onItemCreate = dojo.hitch(this, 'setupBidCard');
                this.lotBidStock.container_div.width = "200px"; // enought just for 1 card
                this.lotBidStock.autowidth = false;

                this.blockBidStock.onItemCreate = dojo.hitch(this, 'setupBidCard');
                this.blockBidStock.container_div.width = "200px"; // enought just for 1 card
                this.blockBidStock.autowidth = false;

                this.qualityBidStock.onItemCreate = dojo.hitch(this, 'setupBidCard');
                this.qualityBidStock.container_div.width = "200px"; // enought just for 1 card
                this.qualityBidStock.autowidth = false;
                /*this.bidStock.onItemCreate = dojo.hitch(this, 'setupBidCard');
                this.bidStock.container_div.width = "200px"; // enought just for 1 card
                this.bidStock.autowidth = false; // this is required so it obeys the width set above*/

                this.qualityStock.onItemCreate = dojo.hitch(this, 'setupQualityCard');

                //stock stocks
                for (i in gamedatas.lots) {
                    //console.log(gamedatas.lots + ' ' + gamedatas.lots[i]['type_arg']);
                    this.lotStock.addToStockWithId(gamedatas.lots[i]['type_arg'], gamedatas.lots[i]['type_arg']);
                    this.placeLotMarker(gamedatas.lots[i]['type_arg'], '', true, gamedatas.buildings);
                }
                for (i in gamedatas.blocks) {
                    //console.log(gamedatas.blocks[i]['type_arg']);
                    this.blockStock.addToStockWithId(gamedatas.blocks[i]['type_arg'], gamedatas.blocks[i]['type_arg']);
                }
                for (i in gamedatas.quality) {
                    //console.log(gamedatas.quality + ' ' + gamedatas.quality[i]['type_arg']);
                    this.qualityStock.addToStockWithId(gamedatas.quality[i]['type_arg'], gamedatas.quality[i]['type_arg']);

                }

                //stock goals
                for (i in gamedatas.goals) {
                    console.log('stocking goal ' + gamedatas.goals + ' ' + gamedatas.goals[i]['type_arg']);
                    this.goalStock.addToStockWithId(gamedatas.goals[i]['type_arg'], gamedatas.goals[i]['type_arg']);
                }

                //stock hand
                for (i in gamedatas.hand) {
                    console.log('stocking hand with ' + gamedatas.hand[i]['type_arg']);
                    this.playerHand.addToStockWithId(gamedatas.hand[i]['type_arg'], gamedatas.hand[i]['type_arg']);
                }

                //stock bid tracker
                if (gamedatas.bids.length == this.player_ids.length || this.player_ids.length == 1) {
                    var bids = gamedatas.bids;
                    var state = 5;
                    for (i in bids) {
                        bids[i]['bid_set'] = this.bid_sets[bids[i]['player']];
                        /*var num = parseInt(bids[i]['num_played'])
                        if (num < state) { //find which order to stock
                            state = num;
                        }*/
                    }
                    console.log('state: ' + state);
                    var orders = this.getPlayerOrder(bids, gamedatas.bid_types);
                    console.log(gamedatas.orders);
                    var lot_order = orders[0];
                    var block_order = orders[1];
                    var quality_order = orders[2];


                    for (o in lot_order) { //stock tracker for lot order
                        var player_id = lot_order[o];
                        for (i in gamedatas.bids) {
                            if (gamedatas.bids[i]['player'] == player_id) {
                                this.player_colors[player_id] = gamedatas.bids[i]['color'];
                                //console.log('color: ' + gamedatas.bids[i]['color']);

                                //detect tie to display proper value
                                var tie = false;

                                for (j in gamedatas.bids) {
                                    if (gamedatas.bids[i]['lot_bid'] == gamedatas.bids[j]['lot_bid'] && i != j) {
                                        tie = true;
                                    }
                                }
                                if (gamedatas.bids.length == 1) {
                                    tie = true;
                                }
                                this.alternate_bid = -1;
                                if (tie) { //set alternate bid for lot trackers
                                    this.alternate_bid = gamedatas.bid_types[gamedatas.bids[i]['bid_id']][this.bid_sets[player_id]].split(' / ')[0];
                                    console.log('alternate bid ' + this.alternate_bid);
                                }


                                if (gamedatas.bids[i]['num_played'] < 1) { //add to lot tracker stock
                                    this.lotBidStock.addToStockWithId(gamedatas.bids[i]['lot_bid'], gamedatas.bids[i]['player']);
                                    //this.bidStock.addToStockWithId(gamedatas.bids[i]['lot_bid'], gamedatas.bids[i]['player']);
                                } else if (gamedatas.bids[i]['num_played'] < 4) {

                                    Object.values(gamedatas.players).forEach(player => { //place marker for chosen lots
                                        if (player.id == player_id) {
                                            var lot_id = player.lot_pick;
                                            //console.log('adding tracker for player ' + player_id + ' and lot ' + lot_id);
                                            this.placeLotMarker(lot_id, this.player_colors[player_id], false, gamedatas.buildings);
                                        }
                                    })
                                    if (player_id == 0) {
                                        Object.values(gamedatas.otto).forEach(otto => { //place marker for chosen lots

                                            var lot_id = otto.lot_pick;
                                            //console.log('adding tracker for player ' + player_id + ' and lot ' + lot_id);
                                            this.placeLotMarker(lot_id, otto.color, false, gamedatas.buildings);

                                        })
                                    }
                                }
                            }
                        }
                    }

                    for (o in block_order) { //stock tracker for block order
                        var player_id = block_order[o];
                        for (i in gamedatas.bids) {
                            if (gamedatas.bids[i]['player'] == player_id) {
                                this.player_colors[player_id] = gamedatas.bids[i]['color'];
                                //console.log('color: ' + gamedatas.bids[i]['color']);

                                //detect tie to display proper value
                                var tie = false;

                                for (j in gamedatas.bids) {
                                    if (gamedatas.bids[i]['lot_bid'] == gamedatas.bids[j]['lot_bid'] && i != j) {
                                        tie = true;
                                    }
                                }
                                if (gamedatas.bids.length == 1) {
                                    tie = true;
                                }
                                this.alternate_bid = -1;

                                if (tie) { //set alternate bid for block trackers
                                    this.alternate_bid = gamedatas.bid_types[gamedatas.bids[i]['bid_id']][this.bid_sets[player_id]].split(' / ')[1];
                                    console.log('alternate bid ' + this.alternate_bid);
                                }

                                if (gamedatas.bids[i]['num_played'] < 2) { //add to block tracker stock
                                    this.blockBidStock.addToStockWithId(gamedatas.bids[i]['block_bid'], gamedatas.bids[i]['player']);
                                    //this.bidStock.addToStockWithId(gamedatas.bids[i]['block_bid'], gamedatas.bids[i]['player']);
                                }
                            }
                        }
                    }


                    for (o in quality_order) { //stock tracker for quality order
                        var player_id = quality_order[o];
                        for (i in gamedatas.bids) {
                            if (gamedatas.bids[i]['player'] == player_id) {
                                this.player_colors[player_id] = gamedatas.bids[i]['color'];
                                //console.log('color: ' + gamedatas.bids[i]['color']);

                                //detect tie to display proper value
                                var tie = false;

                                for (j in gamedatas.bids) {
                                    if (gamedatas.bids[i]['lot_bid'] == gamedatas.bids[j]['lot_bid'] && i != j) {
                                        tie = true;
                                    }
                                }
                                if (gamedatas.bids.length == 1) {
                                    tie = true;
                                }
                                this.alternate_bid = -1;

                                if (tie) { //set alternate bid for quality trackers
                                    this.alternate_bid = gamedatas.bid_types[gamedatas.bids[i]['bid_id']][this.bid_sets[player_id]].split(' / ')[2];
                                    console.log('alternate bid ' + this.alternate_bid);
                                }

                                if (gamedatas.bids[i]['num_played'] < 3) { //add to quality tracker stock
                                    this.qualityBidStock.addToStockWithId(gamedatas.bids[i]['quality_bid'], gamedatas.bids[i]['player']);
                                    //this.bidStock.addToStockWithId(gamedatas.bids[i]['quality_bid'], gamedatas.bids[i]['player']);
                                }
                            }
                        }
                    }
                    this.alternate_bid = -1;


                }


                for (i in gamedatas.removed) {
                    this.placeLotMarker(gamedatas.removed[i], '');
                }

                document.getElementById('page-title').style.zIndex = 900;

                if (this.show_stars) {
                    this.addStars(this.buildings);
                }

                if (this.isSpectator) { //remove hand if player is spectator
                    var hand = document.getElementById('myhand_wrap');
                    if (hand) {
                        console.log(hand);
                        hand.remove()
                    }
                }

                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();



                console.log("Ending game setup");
            },


            ///////////////////////////////////////////////////
            //// Game & client states

            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function(stateName, args) {
                console.log('Entering state: ' + stateName, args);
                var otto_wait = 1500;


                switch (stateName) {
                    case 'playerBid':
                        if (args.args.bid_count == 0 && this.solo) {
                            /*var tpl = dojo.clone(this.gamedatas.gamestate.args);
                            tpl.you = this.divOtto();
                            var text = ' must play a bid';
                            //title = this.format_string_recursive(text, tpl);
                            var title = this.divOtto() + text;
                            console.log(title);*/
                            this.setDescriptionOnMyTurn('${you} must place a bid', true);
                            setTimeout(() => {
                                this.bgaPerformAction('actOttoBid');
                            }, otto_wait);
                        }
                        break;
                    case 'ottoBid':
                        setTimeout(() => {
                            this.bgaPerformAction('actOttoBid');
                        }, otto_wait);
                        break;
                    case 'playerPickLot':
                        //this.remove(args.args.removed);
                        break;

                    case 'ottoPickLot':
                        this.setDescriptionOnMyTurn('${you} must choose a lot', true);
                        setTimeout(() => {
                            this.bgaPerformAction('actOttoPickLot');
                        }, otto_wait);

                        break;

                    case 'playerPickBlocks':
                        /*if (this.bidStock.count() == 0) {
                            this.stockBidTracker(args.args.bids);
                        }*/

                        break;

                    case 'ottoPickBlocks':
                        this.setDescriptionOnMyTurn('${you} must pick blocks', true);
                        setTimeout(() => {
                            this.bgaPerformAction('actOttoPickBlocks');
                        }, otto_wait);
                        //this.bgaPerformAction('actOttoPickBlocks');
                        break;

                    case 'playerPickQuality':
                        /*if (this.bidStock.count() == 0) {
                            this.stockBidTracker(args.args.bids);
                        }*/
                        break;

                    case 'ottoPickQuality':
                        this.setDescriptionOnMyTurn('${you} must choose a quality', true);
                        setTimeout(() => {
                            this.bgaPerformAction('actOttoPickQuality');
                        }, otto_wait);
                        //this.bgaPerformAction('actOttoPickQuality');
                        break;

                    case 'playerTurn':
                        var items = this.qualityStock.getAllItems();

                        items.forEach(item => {
                            console.log(args.args.quality_id + ' ' + item.id);
                            if (args.args.quality_id == item.id) {
                                console.log('updating quality color to ' + args.args.color);
                                var quality_div = document.getElementById(this.qualityStock.getItemDivId(item.id));
                                quality_div.style.border = '5px solid ' + args.args.color;
                            }
                        })

                        break;
                    case 'ottoTurn':
                        this.setDescriptionOnMyTurn('${you} must construct a building; Height: ', true);
                        break;

                    case 'gameEnd':
                        /*setTimeout(() => {
                            if (this.solo) {
                                var player_score = 0;
                                var otto_score = 0;
                                Object.values(this.gamedatas.players).forEach(player => {
                                    player_score = parseInt(player.score);
                                    console.log('player score is ' + player_score);
                                });
                                Object.values(this.gamedatas.otto).forEach(otto => {
                                    otto_score = parseInt(otto.score);
                                    console.log('otto score is ' + otto_score);
                                });
                                if (player_score > otto_score) {
                                    this.setMainTitle('End of game: ' + otto_score + ' points');
                                } else {
                                    this.setMainTitle('End of game: ' + player_score + ' points');
                                }
                            }
                        }, 300);*/
                        break;

                    case 'dummy':
                        break;
                }
            },

            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function(stateName) {
                console.log('Leaving state: ' + stateName);
                this.no_selection();

                switch (stateName) {

                    case 'playerPickLot':
                        var possible_lots = document.getElementsByClassName('whitearrow');
                        Array.from(possible_lots).forEach(marker => { //replace lot markers with clones to remove click actions
                            const originalElement = marker;

                            // Create a deep clone of the original element
                            const clonedElement = originalElement.cloneNode(true);

                            // Replace the original element with the cloned element in the DOM
                            originalElement.parentNode.replaceChild(clonedElement, originalElement);
                        });
                        break;


                    case 'dummy':
                        break;
                }
            },

            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //        
            onUpdateActionButtons: function(stateName, args) {
                console.log('onUpdateActionButtons: ' + stateName, args);

                if (this.isCurrentPlayerActive()) {
                    switch (stateName) {
                        case 'playerBid':
                            if (args.bid_count > 0 || this.solo == false) {
                                this.statusBar.addActionButton(_('Bid'), () => this.placeBid(args.player_id, args.player_name));
                                this.playerHand.setSelectionMode(2);
                            } else {
                                this.setMainTitle('Otto must play a bid');
                                this.playerHand.setSelectionMode(0);
                            }

                            break;
                        case 'playerPickLot':

                            if (this.solo) {

                                this.lotStock.setSelectionMode(0);
                                var items = this.lotStock.getAllItems();
                                this.lotStock.selectItem(items[0].id);
                                this.statusBar.addActionButton(_(this.getLotName(items[0].id)), () => this.onSelectLot(items[0].id));
                                if (items.length == 1) {
                                    this.setDescriptionOnMyTurn('${you} must pick the remaining lot');
                                } else {
                                    this.setDescriptionOnMyTurn('${you} must pick the left lot');
                                }
                                //var marker = document.getElementById('possible_lot_marker_' + items[0].id);
                                //marker.style.border = 'medium solid red';
                            } else {

                                var items = this.lotStock.getAllItems(); //make arrows selectable
                                var possible_lots = document.getElementsByClassName('whitearrow');
                                Array.from(possible_lots).forEach(marker => {
                                    marker.style.cursor = 'pointer';
                                    marker.addEventListener("click", (e) => {
                                        var divId = e.target.id;
                                        console.log("Clicked div ID:", divId);
                                        //this.addTooltip(divId, _('Select this lot'), '');
                                        var lot_id = divId.split('_')[3];
                                        //this.selectLotMarker(lot_id);
                                        this.onSelectLot(lot_id);
                                    });
                                });
                                if (items.length == 1) {
                                    this.statusBar.addActionButton(_(this.getLotName(items[0].id)), () => this.onSelectLot(items[0].id));
                                    this.lotStock.setSelectionMode(1);
                                    //this.lotStock.selectItem(items[0].id);
                                    this.setDescriptionOnMyTurn('${you} must pick the remaining lot');
                                    //var marker = document.getElementById('possible_lot_marker_' + items[0].id);
                                    //marker.style.border = 'medium solid red';
                                } else {
                                    //this.statusBar.addActionButton(_('Select'), () => this.onSelectLot(args.player_id, args.player_name));
                                    this.lotStock.setSelectionMode(1); //add button for each lot choice
                                    for (i in items) {
                                        var lot_id = items[i].id;
                                        this.place_button(lot_id, 0);
                                    }
                                }
                            }
                            break;
                        case 'playerPickBlocks':

                            if (this.solo) {
                                this.blockStock.setSelectionMode(0);
                                var items = this.blockStock.getAllItems();
                                this.blockStock.selectItem(items[0].id);
                                this.statusBar.addActionButton(_(this.getBlockName(items[0].id)), () => this.onSelectBlocks(items[0].id));

                                if (items.length == 1) {
                                    this.setDescriptionOnMyTurn('${you} must pick the remaining blocks');
                                } else {
                                    this.setDescriptionOnMyTurn('${you} must pick the left blocks');
                                }
                            } else {

                                var items = this.blockStock.getAllItems();
                                if (items.length == 1) {
                                    this.statusBar.addActionButton(_(this.getBlockName(items[0].id)), () => this.onSelectBlocks(items[0].id));
                                    this.blockStock.setSelectionMode(1);
                                    //this.blockStock.selectItem(items[0].id);
                                    this.setDescriptionOnMyTurn('${you} must pick the remaining blocks');
                                } else {
                                    //this.statusBar.addActionButton(_('Select'), () => this.onSelectBlocks(args));
                                    this.blockStock.setSelectionMode(1); //add button for each block choice
                                    for (i in items) {
                                        var block_id = items[i].id;
                                        this.place_button(block_id, 1);
                                    }
                                }
                            }
                            break;
                        case 'playerPickQuality':

                            if (this.solo) {

                                this.qualityStock.setSelectionMode(0);
                                var items = this.qualityStock.getAllItems();
                                this.qualityStock.selectItem(items[0].id);
                                this.statusBar.addActionButton(_(this.getQualityName(items[0].id)), () => this.onSelectQuality(items[0].id));

                                if (items.length == 1) {
                                    this.setDescriptionOnMyTurn('${you} must pick the remaining quality');
                                } else {
                                    this.setDescriptionOnMyTurn('${you} must pick the left quality');
                                }
                            } else {

                                var items = this.qualityStock.getAllItems();
                                if (items.length == 1) {
                                    this.statusBar.addActionButton(_(this.getQualityName(items[0].id)), () => this.onSelectQuality(items[0].id));
                                    this.qualityStock.setSelectionMode(1);
                                    //this.qualityStock.selectItem(items[0].id);
                                    this.setDescriptionOnMyTurn('${you} must pick the remaining quality');
                                } else {
                                    //this.statusBar.addActionButton(_('Select'), () => this.onSelectQuality(args.player_id, args.player_name));
                                    this.qualityStock.setSelectionMode(1); //add button for each quality choice
                                    for (i in items) {
                                        var id = items[i].id;
                                        this.place_button(id, 2);
                                    }
                                }
                            }
                            break;

                        case 'playerTurn':


                            var lot = args.lot_id;
                            this.chosenLot = lot;
                            var quality = args.quality;
                            this.playerBlockCount = args.blocks;
                            if (args.blocks == 0) {
                                this.statusBar.addActionButton(_('0'), () => this.onPlaceBuilding(0));
                                break;
                            }
                            this.buildingHeight = this.getY(lot);
                            if (quality.split('Build').length > 1) { //add height add-on to height
                                this.buildingHeight += parseInt(quality.split('+')[1].split(' ')[0])
                            }
                            console.log('recieved build options for lot ' + lot + ' of quality ' + quality + ' and max height ' + this.buildingHeight);
                            //this.placeHeightButtons(this.buildingHeight, this.playerBlockCount);

                            for (var i = 1; i <= this.buildingHeight; i++) {
                                if (i <= this.playerBlockCount) {
                                    console.log('adding button ' + i);
                                    switch (i) { //stupid fix ngl
                                        case 1:
                                            this.statusBar.addActionButton(_('1'), () => this.onPlaceBuilding(1));
                                            break;
                                        case 2:
                                            this.statusBar.addActionButton(_('2'), () => this.onPlaceBuilding(2));
                                            break;
                                        case 3:
                                            this.statusBar.addActionButton(_('3'), () => this.onPlaceBuilding(3));
                                            break;
                                        case 4:
                                            this.statusBar.addActionButton(_('4'), () => this.onPlaceBuilding(4));
                                            break;
                                        case 5:
                                            this.statusBar.addActionButton(_('5'), () => this.onPlaceBuilding(5));
                                            break;
                                        case 6:
                                            this.statusBar.addActionButton(_('6'), () => this.onPlaceBuilding(6));
                                            break;
                                        case 7:
                                            this.statusBar.addActionButton(_('7'), () => this.onPlaceBuilding(7));
                                            break;
                                    }

                                }
                            }
                            //const playableCardsIds = args.playableCardsIds; // returned by the argPlayerTurn

                            // Add test action buttons in the action status bar, simulating a card click:
                            /*playableCardsIds.forEach(
                                cardId => this.statusBar.addActionButton(_('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId))
                            );*/


                            break;

                        case 'ottoTurn':


                            var lot = args.lot_id;
                            this.chosenLot = lot;
                            var quality = args.quality;
                            this.playerBlockCount = args.blocks;
                            this.buildingHeight = this.getY(lot);
                            if (quality.split('Build').length > 1) { //add height add-on to height
                                this.buildingHeight += parseInt(quality.split('+')[1].split(' ')[0])
                            }
                            if (args.blocks == 0) {
                                this.statusBar.addActionButton(_('0'), () => this.onOttoPlaceBuilding(0));
                                break;
                            }
                            console.log('recieved build options for lot ' + lot + ' of quality ' + quality + ' and max height ' + this.buildingHeight);
                            //this.placeHeightButtons(this.buildingHeight, this.playerBlockCount);

                            for (var i = 1; i <= this.buildingHeight; i++) {
                                if (i <= this.playerBlockCount) {
                                    console.log('adding button ' + i);
                                    switch (i) { //stupid fix ngl
                                        case 1:
                                            this.statusBar.addActionButton(_('1'), () => this.onOttoPlaceBuilding(1));
                                            break;
                                        case 2:
                                            this.statusBar.addActionButton(_('2'), () => this.onOttoPlaceBuilding(2));
                                            break;
                                        case 3:
                                            this.statusBar.addActionButton(_('3'), () => this.onOttoPlaceBuilding(3));
                                            break;
                                        case 4:
                                            this.statusBar.addActionButton(_('4'), () => this.onOttoPlaceBuilding(4));
                                            break;
                                        case 5:
                                            this.statusBar.addActionButton(_('5'), () => this.onOttoPlaceBuilding(5));
                                            break;
                                        case 6:
                                            this.statusBar.addActionButton(_('6'), () => this.onOttoPlaceBuilding(6));
                                            break;
                                        case 7:
                                            this.statusBar.addActionButton(_('7'), () => this.onOttoPlaceBuilding(6));
                                            break;
                                    }

                                }
                            }
                            break;

                        case 'playerAddOn':
                            this.placeAddOnMarkers(args.buildings);
                            /*for (i in args.buildings) {
                                this.placeMarker(parseInt(args.buildings[i]), 0);
                            }*/
                            this.statusBar.addActionButton(_('Pass'), () => this.onSkip());
                            break;
                        case 'playerRemodel':
                            this.placeRemodelMarkers(args.buildings);
                            /*for (i in args.buildings) {
                                this.placeMarker(parseInt(args.buildings[i]), 1);
                            }*/
                            this.statusBar.addActionButton(_('Pass'), () => this.onSkip());
                            break;
                        case 'ottoAddOn':
                            this.setDescriptionOnMyTurn('${you} may add one block to a building', true);
                            this.placeAddOnMarkers(args.buildings, true);
                            this.statusBar.addActionButton(_('Pass'), () => this.onSkip(true));
                            break;
                        case 'ottoRemodel':
                            this.setDescriptionOnMyTurn('${you} may select a building to remodel', true);
                            this.placeRemodelMarkers(args.buildings, true);
                            this.statusBar.addActionButton(_('Pass'), () => this.onSkip(true));
                            break;
                    }
                }
            },

            ///////////////////////////////////////////////////
            //// Utility methods

            /*
        
                Here, you can defines some utility methods that you can use everywhere in your javascript
                script.
        
            */
            setupBidCard: function(card_div, card_type_id, card_id) {
                /* Add some custom HTML content INSIDE the Stock item:
                dojo.place( this.format_block( 'jstpl_my_card_content', {
                                            "<div>"
                                    } ), card_div.id );*/

                var player_id = card_id.split('_')[2];
                var color = this.player_colors[player_id];
                var player_name = '';
                Object.values(this.gamedatas.players).forEach(player => {
                    if (player.id == player_id) {
                        player_name = player.name;
                    }
                });
                if (player_name == '') {
                    player_name = 'Otto';
                }
                switch (color) {
                    case 'blue':
                        color = '#7fadc3';
                        break;
                    case 'green':
                        color = '#8fbe91';
                        break;
                    case 'red':
                        color = '#d48484';
                        break;
                    case 'yellow':
                        color = '#ecda81';
                        break;
                    case 'orange':
                        color = '#ebb97d';
                        break;
                    case 'purple':
                        color = '#a488bb';
                        break;
                }

                console.log("setting up bid " + card_type_id + " for player " + player_id + ' ' + player_name + ' of color ' + color);
                if (this.alternate_bid != -1) {
                    //card_div.innerText = card_type_id + ' (' + this.alternate_bid + ')';
                    card_div.innerText = card_type_id + ' (' + this.alternate_bid + '): ' + player_name;
                } else {
                    //card_div.innerText = card_type_id
                    card_div.innerText = card_type_id + ': ' + player_name;
                }
                if (this.playerCount > 4) {
                    card_div.style.fontSize = "12px";
                } else {
                    card_div.style.fontSize = "14px";
                }
                card_div.style.fontWeight = "bold";
                card_div.style.backgroundColor = color;
            },



            setupHandCard: function(card_div, card_type_id, card_id) {
                this.addTooltip(card_div.id, _('Choose a bid and a booster'), '');
            },

            setupStockCard: function(card_div, card_type_id, card_id) {
                if (card_type_id < 36) {
                    this.addTooltip(card_div.id, _('Choose a lot for your building'), '');
                } else if (card_type_id < 56) {
                    this.addTooltip(card_div.id, _('Choose blocks'), '');
                } else {
                    this.addTooltip(card_div.id, _('Choose a quality for your building'), '');
                }

            },

            setupQualityCard: function(card_div, card_type_id, card_id) {
                Object.values(this.gamedatas.players).forEach(player => {
                    //console.log(player.quality_id + ' ' + card_type_id);
                    if (player.quality_id == card_type_id) {
                        console.log('updating quality color to ' + player.color);
                        card_div.style.border = '5px solid #' + player.color;
                    }
                });
            },

            stockBidTracker: function(bids) {
                //var bids = this.gamedatas.bids;
                var state = 5;
                for (i in bids) {
                    bids[i]['bid_set'] = this.bid_sets[bids[i]['player']];
                    var num = parseInt(bids[i]['num_played'])
                    if (num < state) { //find which order to stock
                        state = num;
                    }
                }
                console.log('state: ' + state);
                var orders = this.getPlayerOrder(bids, this.gamedatas.bid_types);
                //console.log(gamedatas.orders);
                var order = orders[state];



                for (o in order) { //stock tracker for lot order
                    var player_id = order[o];
                    for (i in bids) {
                        if (bids[i]['player'] == player_id) {
                            this.player_colors[player_id] = bids[i]['color'];
                            //console.log('color: ' + gamedatas.bids[i]['color']);

                            //detect tie to display proper value
                            var tie = false;

                            for (j in bids) {
                                if (bids[i]['lot_bid'] == bids[j]['lot_bid'] && i != j) {
                                    tie = true;
                                }
                            }
                            if (bids.length == 1) {
                                tie = true;
                            }
                            this.alternate_bid = -1;
                            if (tie) { //set alternate bid
                                this.alternate_bid = this.gamedatas.bid_types[bids[i]['bid_id']][this.bid_sets[player_id]].split(' / ')[state];
                                console.log('alternate bid ' + this.alternate_bid);
                            }


                            if (bids[i]['num_played'] <= state) { //add to lot tracker stock
                                //this.lotBidStock.addToStockWithId(gamedatas.bids[i]['lot_bid'], gamedatas.bids[i]['player']);
                                var bid_name = '';
                                switch (state) {
                                    case 0:
                                        bid_name = 'lot_bid';
                                        break;
                                    case 1:
                                        bid_name = 'block_bid';
                                        break;
                                    case 2:
                                        bid_name = 'quality_bid';
                                        break;
                                }
                                this.bidStock.addToStockWithId(bids[i][bid_name], bids[i]['player']);
                            }

                        }
                    }
                }

            },

            getRandomIntInclusive: function(min, max) {
                min = Math.ceil(min);
                max = Math.floor(max);
                return Math.floor(Math.random() * (max - min + 1)) + min;
            },

            remove: function(ids) {
                console.log('removing ' + ids);
                for (i in ids) {
                    //this.lotStock.removeFromStockById(parseInt(ids[i]));
                    //this.blockStock.removeFromStockById(parseInt(ids[i]));
                    this.playerHand.removeFromStockById(parseInt(ids[i]));
                }
            },

            no_selection: function() {
                this.playerHand.setSelectionMode(0);
                this.lotStock.setSelectionMode(0);
                this.blockStock.setSelectionMode(0);
                this.qualityStock.setSelectionMode(0);
            },

            place_button: function(id, type, otto = false) {
                switch (type) {
                    case 0: //add lot button
                        this.statusBar.addActionButton(_(this.getLotName(id)), () => this.onSelectLot(id));
                        break;
                    case 1: //add block button
                        this.statusBar.addActionButton(_(this.getBlockName(id)), () => this.onSelectBlocks(id));
                        break;
                    case 2: //add quality button
                        this.statusBar.addActionButton(_(this.getQualityName(id)), () => this.onSelectQuality(id));
                        break;
                    case 3: //addon button
                        if (otto) {
                            this.statusBar.addActionButton(_(this.getLotName(id, false)), () => this.onOttoAddOn(id));
                        } else {
                            this.statusBar.addActionButton(_(this.getLotName(id, false)), () => this.onAddOn(id));
                        }
                        break;
                    case 4: //add remodel button
                        if (otto) {
                            this.statusBar.addActionButton(_(this.getLotName(id, false)), () => this.onOttoRemodel(id));
                        } else {
                            this.statusBar.addActionButton(_(this.getLotName(id, false)), () => this.onRemodel(id));
                        }
                        break;
                }
            },

            addStars: function(buildings = []) {
                var offset = 30;
                if (buildings.length == 0) {
                    buildings = this.buildings;
                }
                this.removeStars();
                //console.log(this.gamedatas.buildings.length);
                for (i in buildings) {
                    var building = buildings[i];
                    var lot_id = building.lot_id;
                    var quality = building.quality;
                    var points = building.points;
                    var height = building.height;
                    var counted_blocks = points / quality;
                    var start_stars = height - counted_blocks;
                    console.log('placing stars for lot ' + lot_id);
                    for (j = 0; j < counted_blocks; j++) {
                        var y = start_stars + j;
                        var block = document.getElementById('front_building_' + lot_id + '_' + y);
                        if (block) {
                            for (p = 0; p < quality; p++) {
                                block.insertAdjacentHTML(`afterbegin`, `<div id="star_${lot_id}_${y}_${p}" class="buildingstar"></div>`);
                                var star = document.getElementById('star_' + lot_id + '_' + y + '_' + p);
                                var adjust = offset * p;
                                star.style.transform = 'translate(' + adjust + 'px , 0)';
                            }
                        }
                    }
                }
            },

            removeStars: function() {
                document.querySelectorAll('.buildingstar').forEach(element => element.remove());
            },

            placeAddOnMarkers: function(buildings, otto = false) {
                for (i in buildings) {
                    var building = buildings[i];
                    var lot_id = building.lot_id;
                    var height = building.height - 1;
                    this.place_button(lot_id, 3, otto); //place buttons in bar
                    for (j = 0; j <= height; j++) {
                        var block = document.getElementById('front_building_' + lot_id + '_' + j);
                        if (j == height) {
                            block.insertAdjacentHTML(`afterbegin`, `<div id="addonmarker" class="addon"></div>`);
                            console.log(block);
                            console.log('placed addon for lot ' + lot_id);
                        }
                        this.blockClick(block, lot_id, 0, otto);

                    }
                }
            },

            placeRemodelMarkers: function(buildings, otto = false) {
                for (i in buildings) {
                    var building = buildings[i];
                    const lot_id = building.lot_id;
                    const quality = building.quality;
                    const height = building.height - 1;
                    this.place_button(lot_id, 4, otto); //place buttons in bar
                    for (j = 0; j <= height; j++) {
                        var block = document.getElementById('front_building_' + lot_id + '_' + j);

                        if (j == height) {
                            var roof = document.getElementById('roof_' + lot_id);
                            if (quality == 1) {
                                roof.insertAdjacentHTML(`afterbegin`, `<div id="remodelmarker" class="silverremodel"></div>`);
                            } else {
                                roof.insertAdjacentHTML(`afterbegin`, `<div id="remodelmarker" class="goldremodel"></div>`);
                            }
                            console.log('placed remodel for lot ' + lot_id);
                        }
                        this.blockClick(block, lot_id, 1, otto);

                    }
                }
            },

            blockClick: function(block, lot_id, type, otto = false) { //add remodel/addon click action
                block.style.cursor = 'pointer';
                if (otto) {
                    if (type == 0) {
                        block.addEventListener("click", (e) => {
                            this.onOttoAddOn(lot_id);
                        });
                    } else {
                        block.addEventListener("click", (e) => {
                            this.onOttoRemodel(lot_id);
                        });
                    }
                } else {
                    if (type == 0) {
                        block.addEventListener("click", (e) => {
                            this.onAddOn(lot_id);
                        });
                    } else {
                        block.addEventListener("click", (e) => {
                            this.onRemodel(lot_id);
                        });
                    }
                }
            },



            updateBlocks: function(blocks, player_id, block_supply = -1) {
                this.playerBlockCount = blocks;
                document.getElementById('block-counter-' + player_id).innerText = 'Blocks: ' + this.playerBlockCount;
                if (block_supply > -1) {
                    document.getElementById('supply-counter-' + player_id).innerText = 'Supply: ' + block_supply;
                }
                //console.log('block count ' + this.playerBlockCount);
            },



            getX: function(lot_id) {
                if (this.big_board) {
                    var x = lot_id % 5 + 1;
                } else {
                    var x = lot_id % 4 + 1
                }
                return x;
            },

            getY: function(lot_id) {
                if (this.big_board) {
                    var y = Math.floor((lot_id - 20) / 5) + 1;
                } else {
                    var y = Math.floor((lot_id - 20) / 4) + 1;
                }
                return y;
            },

            setMainTitle: function(text) {
                $('pagemaintitletext').innerHTML = text;
            },



            placeFrontViewBuilding: function(player_id, lot_id, quality, height, color, points) {
                //squares_to_place = points / quality;
                squares_to_place = height
                overlap = height - squares_to_place;
                console.log('placing front view building squares ' + squares_to_place + ' for player ' + player_id + ' of color ' + color);
                var x = this.getX(lot_id);
                var y = this.getY(lot_id);

                console.log(x + ' ' + y);
                for (i = 0; i < squares_to_place; i++) {
                    var y2 = i; //height of building block
                    var y3 = i + y; //grid position
                    var front_square = document.getElementById('view_marker_' + x + '_' + y3);

                    if (document.getElementById('front_building_' + lot_id + '_' + y2)) { //check if building exists in square and remove it
                        document.getElementById('front_building_' + lot_id + '_' + y2).remove();
                        console.log('removing building ' + lot_id + ' ' + y2)
                    }
                    var z = 10 - y;
                    switch (color) { //place building block
                        case 'blue':
                            front_square.insertAdjacentHTML(`afterbegin`, `<div id="front_building_${lot_id}_${y2}" class="blueblock frontviewblock" style="z-index: ${z}"></div>`);
                            break;
                        case 'red':
                            front_square.insertAdjacentHTML(`afterbegin`, `<div id="front_building_${lot_id}_${y2}" class="redblock frontviewblock" style="z-index: ${z}"></div>`);
                            break;
                        case 'green':
                            front_square.insertAdjacentHTML(`afterbegin`, `<div id="front_building_${lot_id}_${y2}" class="greenblock frontviewblock" style="z-index: ${z}"></div>`);
                            break;
                        case 'yellow':
                            front_square.insertAdjacentHTML(`afterbegin`, `<div id="front_building_${lot_id}_${y2}" class="yellowblock frontviewblock" style="z-index: ${z}"></div>`);
                            break;
                        case 'orange':
                            front_square.insertAdjacentHTML(`afterbegin`, `<div id="front_building_${lot_id}_${y2}" class="orangeblock frontviewblock" style="z-index: ${z}"></div>`);
                            break;
                        case 'purple':
                            front_square.insertAdjacentHTML(`afterbegin`, `<div id="front_building_${lot_id}_${y2}" class="purpleblock frontviewblock" style="z-index: ${z}"></div>`);
                            break;
                    }


                    if (i == squares_to_place - 1) { //on final place, add roof
                        console.log('placing roof of quality ' + quality);
                        var top_block = document.getElementById('front_building_' + lot_id + '_' + y2);
                        switch (parseInt(quality)) {
                            case 1:
                                top_block.insertAdjacentHTML(`afterbegin`, `<div id="roof_${lot_id}" class="copper_roof frontviewroof" style="z-index: ${z}"></div>`);
                                break;
                            case 2:
                                top_block.insertAdjacentHTML(`afterbegin`, `<div id="roof_${lot_id}" class="silver_roof frontviewroof" style="z-index: ${z}"></div>`);
                                break;
                            case 3:
                                top_block.insertAdjacentHTML(`afterbegin`, `<div id="roof_${lot_id}" class="gold_roof frontviewroof" style="z-index: ${z}"></div>`);
                                break;
                        }

                    }
                    var block = document.getElementById('front_building_' + lot_id + '_' + y2);
                    //var roof = document.getElementById('roof_' + lot_id);
                    var alignment = this.getAlignment(y, y2);
                    var x_adjust = alignment[0];
                    var y_adjust = alignment[1];
                    //console.log(block.style.left);
                    //x_adjust += this.getPixels(block.style.left);
                    //y_adjust += this.getPixels(block.style.bottom);
                    h = '' + x_adjust + 'px';
                    v = '' + y_adjust + 'px';
                    //block.style.left = h;
                    //block.style.bottom = v;
                    block.style.transform += 'scale(0.45) translate(' + h + ',' + v + ')';
                    console.log('set block ' + lot_id + ' ' + y2 + ' to alignmnent ' + h + ' ' + v);
                }
            },

            getPixels: function(offset) {
                console.log(offset);
                var px = parseInt(offset.substring(0, offset.indexOf('px')));
                console.log(px);
                return px;
            },

            setLotName: function(arrow, lot_id) {
                var x = this.getX(lot_id);
                var y = this.getY(lot_id);
                var letter = '';
                switch (x) {
                    case 1:
                        letter = 'A';
                        break;
                    case 2:
                        letter = 'B';
                        break;
                    case 3:
                        letter = 'C';
                        break;
                    case 4:
                        letter = 'D';
                        break;
                    case 5:
                        letter = 'E';
                        break;
                }
                var name = y + letter;
                arrow.innerText = name;
            },

            getLotName: function(lot_id, gelato = true) {
                var x = this.getX(lot_id);
                var y = this.getY(lot_id);
                var letter = '';
                switch (x) {
                    case 1:
                        letter = 'A';
                        break;
                    case 2:
                        letter = 'B';
                        break;
                    case 3:
                        letter = 'C';
                        break;
                    case 4:
                        letter = 'D';
                        break;
                    case 5:
                        letter = 'E';
                        break;
                }
                var name = y + letter;
                if (y == 5 && gelato) {
                    name += ' ' + this.divGelato();
                }
                return name;
            },

            getBlockName: function(block_id) {
                if (this.big_board) {
                    var block_types = this.gamedatas.block_types_bb;
                } else {
                    var block_types = this.gamedatas.block_types;
                }

                var name = block_types[block_id]['card_name'];
                var points = block_types[block_id]['points'];
                if (points == 2) {
                    name += ' ' + this.divGelato() + this.divGelato();
                } else if (points > 0) {
                    name += ' ' + this.divGelato();
                }
                return name;
            },

            getQualityName: function(quality_id) {
                if (this.big_board) {
                    var quality_types = this.gamedatas.quality_types_bb;
                } else {
                    var quality_types = this.gamedatas.quality_types;
                }
                var name = quality_types[quality_id]['card_name'];
                var points = quality_types[quality_id]['points'];
                var build_div = '';
                var space = false
                if (name.split(' ').length > 1 || points > 0) {
                    space = true;
                }
                if (name.indexOf('Bronze') != -1) {
                    build_div = this.divBuild(0, space);
                    name = build_div + name.split('Bronze')[1];
                }
                if (name.indexOf('Silver') != -1) {
                    build_div = this.divBuild(1, space);
                    name = build_div + name.split('Silver')[1];
                }
                if (name.indexOf('Gold') != -1) {
                    build_div = this.divBuild(2, space);
                    name = build_div + name.split('Gold')[1];
                }
                if (points == 2) {
                    name += ' ' + this.divGelato() + this.divGelato();
                } else if (points > 0) {
                    name += ' ' + this.divGelato();
                }
                return name;
            },

            divGelato: function() {
                return "<div class='gelato'></div>"
            },

            divBuild: function(quality, space) {
                if (space) {
                    switch (quality) {
                        case 0:
                            return "<div class=buildcontainer><div class='bronzebuild'></div></div>"
                        case 1:
                            return "<div class=buildcontainer><div class='silverbuild'></div></div>"
                        default:
                            return "<div class=buildcontainer><div class='goldbuild'></div></div>"
                    }
                } else {
                    switch (quality) {
                        case 0:
                            return "<div class=smallbuildcontainer><div class='bronzebuild'></div></div>"
                        case 1:
                            return "<div class=smallbuildcontainer><div class='silverbuild'></div></div>"
                        default:
                            return "<div class=smallbuildcontainer><div class='goldbuild'></div></div>"
                    }
                }

            },

            getAlignment: function(y, y2) { //determine alignment of blocks in 3d view
                y -= 1;
                //this.y_adjust = -83;
                //this.x_adjust = 78;
                var vertical_adjust = y * this.y_adjust;
                var horizontal_adjust = y * this.x_adjust;
                horizontal_adjust -= 4 * y2; //line up blocks vertically
                //console.log(horizontal_adjust + ' ' + vertical_adjust);
                return [horizontal_adjust, vertical_adjust];
            },

            alignElement: function(y, element, resize = 1, adjust = 0) {
                y -= 1;
                var y_adjust = -38 / resize;
                var x_adjust = (35 / resize) + (adjust / resize);
                /*if (resize != 1) {
                    y_adjust = y_a;
                    x_adjust = x_a;

                }*/
                var vertical_adjust = y * y_adjust;
                var horizontal_adjust = y * x_adjust;
                //console.log(horizontal_adjust + ' ' + vertical_adjust);
                h = '' + horizontal_adjust + 'px';
                v = '' + vertical_adjust + 'px';
                if (resize != 1) {
                    element.style.transform = 'scale(' + resize + ') translate(' + h + ', ' + v + ')';
                } else {
                    element.style.transform = 'translate(' + h + ', ' + v + ')';
                }

            },



            placeLotMarker: function(lot_id, color, possible_lot = false, buildings = []) { //place marker for possible lots and removed lots
                var x = this.getX(lot_id);
                var y = this.getY(lot_id);
                var z = 10 - y;
                var resize = 0.6;
                var square = document.getElementById('view_marker_' + x + '_' + y);

                var row = 0;
                var offset = 0;
                var distance = 0;
                if (this.big_board) {
                    row = 5;
                } else {
                    row = 4;
                }
                for (i in buildings) { //find buildings in the way, offset arrow if blocked
                    building = buildings[i];
                    var lot = building.lot_id;
                    if (lot_id > lot && (lot_id - lot) - row == 0) { //building is in front of space
                        console.log('building in ' + lot + ' in front of arrow');
                        //distance = (lot_id - lot) / row;
                        if (building.height >= 4) { //if building tall as hell, add offset
                            offset = 10;
                            console.log(color + 'arrow given offset');
                        }
                    }
                }

                if (possible_lot) { //TODO place possible lot marker arrow

                    console.log('placing lot stock marker at lot ' + lot_id);
                    //square.insertAdjacentHTML(`afterbegin`, `<div id="possible_lot_marker_${lot_id}" class="possiblelotmarker"></div>`);
                    square.insertAdjacentHTML(`afterbegin`, `<div id="possible_lot_marker_${lot_id}" class="whitearrow arrow" style="z-index: ${z}"></div>`);
                    var arrow = document.getElementById('possible_lot_marker_' + lot_id); //place arrow
                    this.alignElement(y, arrow, resize, offset);

                    //square.insertAdjacentHTML(`afterbegin`, `<div id="transparent_lot_marker_${lot_id}" class="whitearrow arrow frontarrow"></div>`);
                    //var front_arrow = document.getElementById('transparent_lot_marker_' + lot_id);
                    //this.alignElement(y, front_arrow, resize);
                    //console.log(front_arrow);
                } else {
                    if (color == '') { //remove lot
                        console.log('removing lot ' + lot_id);

                        //console.log(square);
                        square.insertAdjacentHTML(`afterbegin`, `<div id="lot_marker_${lot_id}" class="removedlot"></div>`);
                        var marker = document.getElementById('lot_marker_' + lot_id);
                        //console.log(marker);
                        this.alignElement(y, marker);
                    } else {
                        console.log('placing marker at lot ' + this.getLotName(lot_id));

                        var possible_marker = document.getElementById('possible_lot_marker_' + lot_id)
                        if (possible_marker) {
                            possible_marker.remove();
                        }

                        square.insertAdjacentHTML(`afterbegin`, `<div id="lot_marker_${lot_id}" class="${color}arrow arrow" style="z-index: ${z}"></div>`);
                        var arrow = document.getElementById('lot_marker_' + lot_id);
                        this.alignElement(y, arrow, resize, offset);
                        //console.log(arrow);

                    }

                }
                if (arrow) {
                    this.setLotName(arrow, lot_id);
                }
                /*if (front_arrow) {
                    this.setLotName(front_arrow, lot_id);
                }*/
            },

            /*selectLotMarker: function(lot_id) {
                this.lotStock.unselectAll();
                this.lotStock.selectItem(lot_id);
                var possible_lots = document.getElementsByClassName('possiblelotmarker');
                Array.from(possible_lots).forEach(marker => {
                    marker.style.border = '';
                });
                var marker = document.getElementById('possible_lot_marker_' + lot_id);
                marker.style.border = 'medium solid red';
            },*/

            getPlayerOrder: function(bids, bid_types) { // breaks ties in bids, order doesn't matter otherwise (displayed correctly in stock regardless)
                var orders = [];
                var ties = [];

                //lot order
                for (i in bids) {
                    for (j in bids) {
                        if (bids[i]['lot_bid'] == bids[j]['lot_bid'] && i != j) { //tie detected
                            var tie = {};
                            tie['player'] = bids[i]['player'];
                            var alternate_bids = bid_types[bids[i]['bid_id']][bids[i]['bid_set']].split(' / ');
                            tie['lot_bid'] = alternate_bids[0];
                            tie['block_bid'] = alternate_bids[1];
                            tie['quality_bid'] = alternate_bids[2];
                            ties.push(tie);
                            break;
                        }
                    }
                }
                console.log('ties: ' + ties);
                var order = [];

                if (ties.length > 0) {
                    ties.sort((a, b) => b['lot_bid'] - a['lot_bid']); //lot bids
                    for (i in ties) {
                        order.push(ties[i]['player']);
                    }
                    orders.push(order);
                    order = [];

                    ties.sort((a, b) => b['block_bid'] - a['block_bid']); //block bids
                    for (i in ties) {
                        order.push(ties[i]['player']);
                    }
                    orders.push(order);
                    order = [];

                    ties.sort((a, b) => b['quality_bid'] - a['quality_bid']); //quality bids
                    for (i in ties) {
                        order.push(ties[i]['player']);
                    }
                    orders.push(order);
                    order = [];
                } else {
                    orders.push([]);
                    orders.push([]);
                    orders.push([]);
                }

                for (i in bids) { //add remaining players to order
                    var player_id = bids[i]['player'];
                    if (orders[0] != undefined) {
                        if (orders[0].indexOf(player_id) == -1) {
                            orders[0].push(player_id);
                            orders[1].push(player_id);
                            orders[2].push(player_id);
                        }
                    }

                }
                console.log(orders);

                return orders;


            },

            divOtto: function() {
                var color = '';
                Object.values(this.gamedatas.otto).forEach(otto => {
                    switch (otto.color) {
                        case 'blue':
                            color = '#5391ae';
                            break;
                        case 'green':
                            color = '#65a668';
                            break;
                        case 'red':
                            color = '#c25151';
                            break;
                        case 'yellow':
                            color = '#e2c742';
                            break;
                        case 'orange':
                            color = '#e1973f';
                            break;
                        case 'purple':
                            color = '#855fa3';
                            break;
                    }
                });
                //console.log('otto color is ' + color);

                //var color = this.gamedatas.otto['Otto Amalfi'].color;
                var color_bg = "background-color:#" + color + ';';
                /*if (this.gamedatas.players[player_id] && this.gamedatas.players[player_id].color_back) {
                    color_bg = "background-color:#" + this.gamedatas.players[player_id].color_back + ";";
                }*/
                var div = "<span style=\"color:" + color + ";" + color_bg + "\">" + __("lang_mainsite", "Otto") + "</span>";
                return div;
            },

            divYou: function() {
                var color = this.gamedatas.players[this.player_id].color;
                var color_bg = "";
                if (this.gamedatas.players[this.player_id] && this.gamedatas.players[this.player_id].color_back) {
                    color_bg = "background-color:#" + this.gamedatas.players[this.player_id].color_back + ";";
                }
                var you = "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + __("lang_mainsite", "You") + "</span>";
                return you;
            },

            setDescriptionOnMyTurn: function(text, otto = false) {
                this.gamedatas.gamestate.descriptionmyturn = text;
                var tpl = dojo.clone(this.gamedatas.gamestate.args);
                if (tpl === null) {
                    tpl = {};
                }
                var title = "";
                if (otto) {
                    console.log('setting description for otto');
                    tpl.you = this.divOtto();
                } else {
                    if (this.isCurrentPlayerActive() && text !== null) {
                        tpl.you = this.divYou();
                    }
                }
                title = this.format_string_recursive(text, tpl);

                if (!title) {
                    this.setMainTitle(" ");
                } else {
                    console.log('setting main title to ' + title);
                    this.setMainTitle(title);
                }
            },





            ///////////////////////////////////////////////////
            //// Player's action

            /*
        
                Here, you are defining methods to handle player's action (ex: results of mouse click on 
                game objects).
                
                Most of the time, these methods:
                _ check the action is possible at this game state.
                _ make a call to the game server
        
            */



            //onPlayerHandSelectionChanged: function() {
            //var items = this.playerHand.getSelectedItems();

            /*if (items.length > 0) {
                if (this.checkAction("actPlayCard", true)) {
                    // Can play a card

                    var card_id = items[0].id;
                    console.log("on playCard " + card_id);

                    this.playerHand.unselectAll();
                } else if (this.checkAction("actGiveCards")) {
                    // Can give cards => let the player select some cards
                } else {
                    this.playerHand.unselectAll();
                }
            }*/


            //place bid on table when player selects play
            placeBid: function() {
                var items = this.playerHand.getSelectedItems();
                if (items.length != 2) {
                    this.showMessage(_('You must select one bid and one booster to bid!'), 'error');
                } else if (items[0].id <= 7 && items[1].id > 7) {
                    var bid_id = items[0].id;
                    var booster_id = items[1].id;
                    console.log('playing cards ' + bid_id + ' ' + booster_id);
                    //this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + bid_id);
                    this.playerHand.removeFromStockById(bid_id);


                    //this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + booster_id);
                    this.playerHand.removeFromStockById(booster_id);

                    this.bgaPerformAction("actPlayBid", { bid_id: bid_id, booster_id: booster_id });
                } else {
                    this.showMessage(_('You must select one bid and one booster to bid!'), 'error');
                }


            },

            onSelectLot: function(lot_id = 0) {
                if (lot_id != 0) {
                    this.bgaPerformAction("actPickLot", { lot_id: lot_id });
                } else {
                    var items = this.lotStock.getSelectedItems();
                    if (items.length == 1) {
                        this.lotStock.removeFromStockById(items[0].id);
                        this.bgaPerformAction("actPickLot", { lot_id: items[0].id });
                    }
                }
            },

            onLotStockSelection: function(control_name, item_id) {
                this.onSelectLot();
            },

            onBlockStockSelection: function(control_name, item_id) {
                this.onSelectBlocks();
            },

            onQualityStockSelection: function(control_name, item_id) {
                this.onSelectQuality();
            },

            onSelectBlocks: function(block_id = 0) {
                if (block_id != 0) {
                    this.bgaPerformAction("actPickBlocks", { id: block_id, selection: -1 });
                } else {
                    var items = this.blockStock.getSelectedItems();
                    if (items.length == 1) {
                        this.blockStock.removeFromStockById(items[0].id);
                        this.bgaPerformAction("actPickBlocks", { id: items[0].id, selection: -1 });
                    }
                }
            },

            onSelectQuality: function(id = 0) {
                if (id != 0) {
                    var player_id = this.gamedatas.player_id;
                    var quality_div = document.getElementById(this.qualityStock.getItemDivId(id));
                    quality_div.style.border = '5px solid ' + this.player_colors[player_id];

                    this.bgaPerformAction("actPickQuality", { id: id });
                } else {
                    var items = this.qualityStock.getSelectedItems();
                    if (items.length == 1) {
                        //this.qualityStock.removeFromStockById(items[0].id);
                        var player_id = this.gamedatas.player_id;
                        var quality_div = document.getElementById(this.qualityStock.getItemDivId(items[0].id));
                        quality_div.style.border = '5px solid ' + this.player_colors[player_id];

                        this.bgaPerformAction("actPickQuality", { id: items[0].id });
                    }
                }
            },


            onPlaceBuilding: function(height) {
                console.log('placing building height ' + height);
                //this.placeBuilding(args.player_id, this.chosenLot, args.quality_pick[args.player_id], this.buildingHeight, "#AA0000");
                this.bgaPerformAction("actBuild", { lot_id: this.chosenLot, height: height });
            },

            onOttoPlaceBuilding: function(height) {
                console.log('placing building height ' + height);
                //this.placeBuilding(args.player_id, this.chosenLot, args.quality_pick[args.player_id], this.buildingHeight, "#AA0000");
                this.bgaPerformAction("actOttoBuild", { lot_id: this.chosenLot, height: height });
            },

            onAddOn: function(lot_id) {
                console.log('adding on to building ' + lot_id);

                this.bgaPerformAction("actAddOn", { lot_id: lot_id });
            },

            onOttoAddOn: function(lot_id) {
                console.log('adding on to building ' + lot_id);

                this.bgaPerformAction("actOttoAddOn", { lot_id: lot_id });
            },

            onRemodel: function(lot_id) {
                console.log('remodeling building ' + lot_id)
                this.bgaPerformAction("actRemodel", { lot_id: lot_id });
            },

            onOttoRemodel: function(lot_id) {
                console.log('remodeling building ' + lot_id)
                this.bgaPerformAction("actOttoRemodel", { lot_id: lot_id });
            },

            onSkip: function(otto = false) {
                this.bgaPerformAction("actPass", { otto: otto });
            },


            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications

            /*
                    setupNotifications:
                    
                    In this method, you associate each of your game notifications with your local method to handle it.
                    
                    Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                          your positano.game.php file.
        
                */
            setupNotifications: function() {
                console.log('notifications subscriptions setup');

                // TODO: here, associate your game notifications with local methods

                // Example 1: standard notification handling
                dojo.subscribe('buildingPlaced', this, "notif_buildingPlaced");
                dojo.subscribe('lotPicked', this, "notif_lotPicked");
                dojo.subscribe('blocksPicked', this, "notif_blocksPicked");
                dojo.subscribe('qualityPicked', this, "notif_qualityPicked");
                dojo.subscribe('removeQuality', this, "notif_removeQuality");
                dojo.subscribe('stocksStocked', this, "notif_stocksStocked");
                dojo.subscribe('handStocked', this, "notif_handStocked");
                dojo.subscribe('playerBlockChoice', this, "notif_playerBlockChoice");
                dojo.subscribe('buildingRennovated', this, "notif_buildingRennovated");
                dojo.subscribe('pointsUpdated', this, "notif_pointsUpdated");
                dojo.subscribe('bidPlayed', this, "notif_bidPlayed");
                dojo.subscribe('stockBidTracker', this, "notif_stockBidTracker");
                dojo.subscribe('lotRemoved', this, "notif_lotRemoved");
                dojo.subscribe('pass', this, "notif_pass");
                dojo.subscribe('buildingSkipped', this, "notif_buildingSkipped");
                dojo.subscribe('solo_endgame', this, "notif_solo_endgame");
                dojo.subscribe('endScores', this, "notif_endScores");

                // Example 2: standard notification handling + tell the user interface to wait
                //            during 3 seconds after calling the method in order to let the players
                //            see what is happening in the game.
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
                // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
                // 
            },




            notif_buildingPlaced: function(notif) {
                console.log('notif_buildingPlaced');
                console.log(notif);
                //this.lotTrackerStock.removeFromStockById(notif.args.player_id);
                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
                //this.qualityStock.removeFromStockById(notif.args.quality_id);
                this.updateBlocks(notif.args.blocks, notif.args.player_id);
                //this.placeBuilding(notif.args.player_id, notif.args.lot_id, notif.args.quality, notif.args.height, notif.args.color, notif.args.points);
                this.placeFrontViewBuilding(notif.args.player_id, notif.args.lot_id, notif.args.quality, notif.args.height, notif.args.color, notif.args.points);
                lotmarker = document.getElementById("lot_marker_" + notif.args.lot_id);
                lotmarker.remove();
                this.buildings = notif.args.buildings;
                if (this.show_stars) {
                    this.addStars(this.buildings);
                }
                if (this.big_board && notif.args.lot_id > 34) { //bring board back to normal height if building is in back row(s)
                    document.getElementById('entire-board-container').style.transform = 'translate(0, 0)';
                }
                if (!this.big_board && notif.args.lot_id > 31) {
                    document.getElementById('entire-board-container').style.transform = 'translate(0, 0)';
                }
            },

            notif_lotPicked: function(notif) {
                console.log('notif_lotPicked');
                console.log(notif);

                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call

                this.lotStock.removeFromStockById(notif.args.id);
                this.lotBidStock.removeFromStockById(notif.args.player_id);
                //this.bidStock.removeFromStockById(notif.args.player_id);
                //this.lotTrackerStock.addToStockWithId(notif.args.id, notif.args.player_id);
                this.buildings = notif.args.buildings;
                this.placeLotMarker(notif.args.id, notif.args.color, false, notif.args.buildings);

                var possible_lots = document.getElementsByClassName('whitearrow'); //remove cursor from possible lots
                Array.from(possible_lots).forEach(marker => {
                    marker.style.cursor = 'default';
                });
            },

            notif_blocksPicked: function(notif) {
                console.log('notif_blocksPicked');
                console.log(notif);

                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
                this.updateBlocks(notif.args.blocks, notif.args.player_id, notif.args.block_supply);
                this.blockStock.removeFromStockById(notif.args.id);
                this.blockBidStock.removeFromStockById(notif.args.player_id);
                //this.bidStock.removeFromStockById(notif.args.player_id);
            },

            notif_qualityPicked: function(notif) {
                console.log('notif_qualityPicked');
                console.log(notif);

                this.qualityBidStock.removeFromStockById(notif.args.player_id);
                //this.bidStock.removeFromStockById(notif.args.player_id);

                console.log(notif.args.player_id + ' ' + this.gamedatas.player_id);
                if (notif.args.player_id != this.gamedatas.player_id) {
                    var quality_div = document.getElementById(this.qualityStock.getItemDivId(notif.args.id));
                    quality_div.style.border = '5px solid ' + notif.args.color;
                    console.log('setting quality to ' + notif.args.color);
                }


            },

            notif_removeQuality: function(notif) {
                console.log('notif_removeQuality');
                console.log(notif);
                this.qualityStock.removeFromStockById(notif.args.quality_id);
            },

            notif_stocksStocked: function(notif) {
                console.log('notif_stocksStocked');
                console.log(notif);

                var temp_stock = notif.args.lot_stock;
                for (i in temp_stock) {
                    console.log('stocking ' + temp_stock[i]['type_arg'])
                    this.lotStock.addToStockWithId(temp_stock[i]['type_arg'], temp_stock[i]['type_arg']);
                    this.placeLotMarker(temp_stock[i]['type_arg'], '', true, notif.args.buildings);
                }
                temp_stock = notif.args.block_stock;
                for (i in temp_stock) {
                    this.blockStock.addToStockWithId(temp_stock[i]['type_arg'], temp_stock[i]['type_arg']);
                }
                temp_stock = notif.args.quality_stock;
                for (i in temp_stock) {
                    this.qualityStock.addToStockWithId(temp_stock[i]['type_arg'], temp_stock[i]['type_arg']);
                }
            },

            notif_handStocked: function(notif) {
                console.log('notif_handStocked');
                console.log(notif);
                var temp_stock = notif.args.player_hand;
                for (i in temp_stock) {
                    console.log('stocking ' + temp_stock[i]['type_arg'])
                    this.playerHand.addToStockWithId(temp_stock[i]['type_arg'], temp_stock[i]['type_arg']);
                }
            },

            notif_playerBlockChoice: function(notif) {
                console.log('notif_playerBlockChoice');
                console.log(notif);
                this.statusBar.removeActionButtons();
                var blocks = notif.args.blocks;
                var x3 = blocks * 3 - blocks;
                var p3 = 3;
                var x2 = blocks * 2 - blocks;
                var p2 = 2;
                var block_supply = notif.args.block_supply;
                if (x3 > block_supply) { //16 block limit
                    x3 = block_supply;
                }
                x3 += blocks;
                if (p3 > block_supply) {
                    p3 = block_supply;
                }
                p3 += blocks;
                if (x2 > block_supply) {
                    x2 = block_supply;
                }
                x2 += blocks;
                if (p2 > block_supply) {
                    p2 = block_supply;
                }
                p2 += blocks;
                if (notif.args.id == 44 || notif.args.id == 46) {
                    this.setMainTitle('3x blocks or +3 blocks?')
                    this.statusBar.addActionButton(_('3x (' + x3 + ')'), () => this.bgaPerformAction("actPickBlocks", { id: notif.args.id, selection: 0 }));
                    this.statusBar.addActionButton(_('+3 (' + p3 + ')'), () => this.bgaPerformAction("actPickBlocks", { id: notif.args.id, selection: 1 }));
                } else {
                    this.setMainTitle('2x blocks or +2 blocks?')
                    this.statusBar.addActionButton(_('2x (' + x2 + ')'), () => this.bgaPerformAction("actPickBlocks", { id: notif.args.id, selection: 0 }));
                    this.statusBar.addActionButton(_('+2 (' + p2 + ')'), () => this.bgaPerformAction("actPickBlocks", { id: notif.args.id, selection: 1 }));
                }


            },


            notif_buildingRennovated: function(notif) {
                console.log('notif_buildingRennovated');
                console.log(notif);
                var x = this.getX(notif.args.lot_id);
                var y = this.getY(notif.args.lot_id);
                //var building = document.getElementById('building_' + x + '_' + y);
                //building.innerText = 'Quality: ' + notif.args.quality + '\nHeight: ' + notif.args.height + '\nPoints: ' + notif.args.points;
                if (notif.args.player_id != -1) {
                    //building.remove();
                    //this.placeBuilding(notif.args.player_id, notif.args.lot_id, notif.args.quality, notif.args.height, notif.args.color, notif.args.points);
                    this.updateBlocks(notif.args.blocks, notif.args.player_id);
                    this.placeFrontViewBuilding(notif.args.player_id, notif.args.lot_id, notif.args.quality, notif.args.height, notif.args.color, notif.args.points);
                    this.buildings = notif.args.buildings;

                    //replace blocks with clones to remove click actions
                    var possible_lots = document.getElementsByClassName('frontviewblock');
                    Array.from(possible_lots).forEach(marker => {
                        const originalElement = marker;

                        // Create a deep clone of the original element
                        const clonedElement = originalElement.cloneNode(true);

                        // Replace the original element with the cloned element in the DOM
                        originalElement.parentNode.replaceChild(clonedElement, originalElement);
                        clonedElement.style.cursor = 'default';
                    });

                    if (this.show_stars) {
                        this.addStars(notif.args.buildings);
                    }
                } else {
                    this.buildings = notif.args.buildings;
                    if (this.show_stars) {
                        this.addStars(notif.args.buildings);
                    }
                    //var point_counter = document.getElementById('point_num_' + notif.args.lot_id);
                    //point_counter.innerText = notif.args.points;
                }



                document.querySelectorAll('.addon').forEach(element => element.remove());
                document.querySelectorAll('.silverremodel').forEach(element => element.remove());
                document.querySelectorAll('.goldremodel').forEach(element => element.remove());

            },

            notif_pointsUpdated: function(notif) {
                console.log('notif_pointsUpdated');
                console.log(notif);
                this.playerPoints = notif.args.points;
                //document.getElementById('point-counter-' + notif.args.player_id).innerText = 'Points: ' + this.playerPoints;
                this.scoreCtrl[notif.args.player_id].toValue(notif.args.points);
            },

            notif_bidPlayed: function(notif) {
                console.log('notif_bidPlayed');
                console.log(notif);
                //console.log('adding bids for player ' + notif.args.player_id);
                this.player_colors[notif.args.player_id] = notif.args.color;

            },

            notif_stockBidTracker: function(notif) {
                console.log('notif_stockBidTracker');
                console.log(notif);

                var bids = notif.args.bids;
                //var state = 5;

                for (i in bids) {
                    bids[i]['bid_set'] = this.bid_sets[bids[i]['player']];
                    bids[i]['alternate_bids'] = notif.args.bid_types[bids[i]['bid_id']][bids[i]['bid_set']];

                    //console.log('setting alternate bid: ' + bids[i]['alternate_bids']);
                }

                var orders = this.getPlayerOrder(bids, notif.args.bid_types);

                for (i in orders[0]) {

                    for (j in bids) {
                        var tie = false;


                        if (orders[0][i] == bids[j]['player']) { //player matched with lot order
                            for (t in bids) { //detect tie
                                if (bids[j]['lot_bid'] == bids[t]['lot_bid'] && j != t) {
                                    tie = true;
                                }
                            }
                            if (bids.length == 1) {
                                tie = true;
                            }
                            this.alternate_bid = -1;
                            if (tie) {
                                this.alternate_bid = bids[j]['alternate_bids'].split(' / ')[0];
                            }
                            this.lotBidStock.addToStockWithId(bids[j]['lot_bid'], bids[j]['player']);
                            //this.bidStock.addToStockWithId(bids[j]['lot_bid'], bids[j]['player']);
                            tie = false;
                        }



                        if (orders[1][i] == bids[j]['player']) { //player matched with block order
                            for (t in bids) { //detect tie
                                if (bids[j]['lot_bid'] == bids[t]['lot_bid'] && j != t) {
                                    tie = true;
                                }
                            }
                            if (bids.length == 1) {
                                tie = true;
                            }
                            this.alternate_bid = -1;
                            if (tie) {
                                this.alternate_bid = bids[j]['alternate_bids'].split(' / ')[1];
                            }
                            this.blockBidStock.addToStockWithId(bids[j]['block_bid'], bids[j]['player']);
                            //this.bidStock.addToStockWithId(bids[j]['block_bid'], bids[j]['player']);
                            tie = false;
                        }

                        if (orders[2][i] == bids[j]['player']) { //player matched with quality order
                            for (t in bids) { //detect tie
                                if (bids[j]['lot_bid'] == bids[t]['lot_bid'] && j != t) {
                                    tie = true;
                                }
                            }
                            if (bids.length == 1) {
                                tie = true;
                            }
                            this.alternate_bid = -1;
                            if (tie) {
                                this.alternate_bid = bids[j]['alternate_bids'].split(' / ')[2];
                            }
                            this.qualityBidStock.addToStockWithId(bids[j]['quality_bid'], bids[j]['player']);
                            //this.bidStock.addToStockWithId(bids[j]['quality_bid'], bids[j]['player']);
                            tie = false;
                        }

                    }
                }


            },

            notif_lotRemoved: function(notif) {
                console.log('notif_lotRemoved');
                console.log(notif);
                this.placeLotMarker(notif.args.lot_id, '');
            },

            notif_pass: function(notif) {
                console.log('notif_pass');
                console.log(notif);
                //replace blocks with clones to remove click actions
                var possible_lots = document.getElementsByClassName('frontviewblock');
                Array.from(possible_lots).forEach(marker => {
                    const originalElement = marker;

                    // Create a deep clone of the original element
                    const clonedElement = originalElement.cloneNode(true);

                    // Replace the original element with the cloned element in the DOM
                    originalElement.parentNode.replaceChild(clonedElement, originalElement);
                    clonedElement.style.cursor = 'default';
                });

                document.querySelectorAll('.addon').forEach(element => element.remove());
                document.querySelectorAll('.silverremodel').forEach(element => element.remove());
                document.querySelectorAll('.goldremodel').forEach(element => element.remove());
                //document.querySelectorAll('.marker').forEach(element => element.remove());
            },

            notif_buildingSkipped: function(notif) {
                console.log('notif_buildingSkipped');
                console.log(notif);
                lotmarker = document.getElementById("lot_marker_" + notif.args.lot_id);
                lotmarker.remove();
                this.placeLotMarker(notif.args.lot_id, '');
            },

            notif_solo_endgame: function(notif) {
                console.log('notif_solo_endgame');
                console.log(notif);
                setTimeout(() => {
                    this.setMainTitle('End of game: ' + notif.args.score + ' points\n');
                }, 200);
            },

            notif_endScores: function(notif) {
                console.log('notif_endScores');
                console.log(notif);
                this.scoreSheet.setScores(notif.args.endScores, {
                    startBy: notif.args.player_id
                });
            },

        });
    }
);