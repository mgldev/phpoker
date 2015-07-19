/**
 * Planning Poker
 * 
 * @author  Michael Leach
 */
var Poker = new Class({

    boards: [],
    currentStory: null,
    connection: null,
    members: [],

    Implements: [Options, Events],

    join: function(username) {

        this.username = username;
        this.connection = new WebSocket("ws://127.0.0.1:8000");
        this.wireConnectionEvents();
    },

    leave: function() {

        this.username = null;
        this.connection.close();
    },

    isConnected: function() {

        return this.connection !== null && this.connection.readyState == 1;
    },

    wireConnectionEvents: function() {

        this.connection.onopen = this.onopen.bind(this);
        this.connection.onmessage = this.onmessage.bind(this);
        this.connection.onclose = this.onclose.bind(this);
        this.connection.onerror = this.onerror.bind(this);
    },

    onopen: function() {

        this.action('login', {
            username: this.username
        });
    },

    onmessage: function(evt) {

        var response = JSON.decode(evt.data);

        switch (response.action) {

            case 'board-list':
                this.boards = response.params.boards;
                this.refreshBoardList();
                $.mobile.changePage('#boards', {
                    transition: "slideup"
                });
                break;

            case 'show-current-story':
                this.currentStory = response.params.story;
                this.refreshCurrentStory();
                $.mobile.changePage('#current-story', {
                    transition: "slideup"
                });
                break;
        }
    },

    action: function(action, params) {

        var request = {
            action: action,
            params: params
        };

        this.connection.send(JSON.encode(request));
    },

    onclose: function() {

        $.mobile.changePage('#connect', {
            transition: "slideup"
        });
    },
    
    refreshCurrentStory: function() {
        
        
    },

    refreshBoardList: function() {

        $('#board-list').empty();

        for (var i = 0; i < this.boards.length; i++) {
            var board = this.boards[i];
            var innerHtml = board.name;
            var theme = '';
            if (board.active) {
                theme = ' data-theme="b"';
                var board_id = 'board-' + board.id;
                innerHtml = '<a id="' + board_id + '" href="#' + board.id + '" title="Join ' + board.name + '">' + board.name + '</a>';
                $('#' + board_id).live('click', function(ev) {
                    ev.preventDefault();
                    this.action('join-board', {
                        board: board.id
                    });
                }.bind(this));
            }
            var item = '<li ' + theme + '>' + innerHtml + '</li>';
            $('#board-list').append(item);
        }
    },

    onerror: function(error) {

        alert('WebSocket Error ' + error);
    },

    setConnectedStatus: function(connected) {

        if (connected) {
            $('#join').find('.ui-btn-text').text('Leave');
        } else {
            $('#join').find('.ui-btn-text').text('Join');
        }
    }
});

$(document).ready(function() {

    var poker = new Poker();

    $('#join').click(function() {
        if (poker.isConnected()) {
            poker.leave();
        } else {
            poker.join($('#username').val());
        }
    });
});