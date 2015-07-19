/**
 * Planning Poker
 * 
 * @author  Michael Leach
 */
var Poker = new Class({

    connection: null,

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
                $.mobile.changePage('#boards', {
                    transition: "slideup"
                });
                this.refreshBoardList(response.params.boards);
                break;

            case 'refresh-board-members':
                this.refreshMemberList(response.params.members);
                break;

            case 'show-current-story':
                $.mobile.changePage('#current-story', {
                    transition: "slideup"
                });
                this.refreshCurrentStory(response.params.story, response.params.board);
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
    
    refreshCurrentStory: function(story, board) {

        var storyName = $('#story-name');
        storyName.find('span.ui-btn-text').html(story.name);
        $('#story-description').html(story.description);
        var storyPosition = $('#story-position');
        storyPosition.find('strong.index').html(board.story_position);
        storyPosition.find('strong.count').html(board.story_count);

        this.refreshMemberList(board.members);
    },

    refreshMemberList: function(members) {

        var memberList = $('ul.board-member-list');

        memberList.empty();

        for (var i = 0; i < members.length; i++) {
            var member = members[i];
            var innerHtml = member.displayName;
            var theme = '';
            var item = '<li ' + theme + '><a href="#">' + innerHtml + '</a></li>';
            memberList.append(item);
        }

        memberList.listview();
        memberList.listview('refresh');
    },

    refreshBoardList: function(boards) {

        var boardList = $('#board-list');
        boardList.empty();

        for (var i = 0; i < boards.length; i++) {
            var board = boards[i];
            var innerHtml = board.name;
            var theme = '';
            if (board.active) {
                theme = ' data-theme="b"';
                var board_id = 'board-' + board.id;
                innerHtml = '<a id="' + board_id + '" href="#' + board.id + '" title="Join ' + board.name + '">' + board.name + '</a>';
                $('#' + board_id).die('click').live('click', function(ev) {
                    ev.preventDefault();
                    this.action('join-board', {
                        board: board.id
                    });
                }.bind(this));
            }
            var item = '<li ' + theme + '>' + innerHtml + '</li>';
            boardList.append(item);
        }

        boardList.listview();
        boardList.listview('refresh');
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
        poker.join($('#username').val());
    });

    $('a.leave-button').click(function() {
        poker.leave();
    })
});