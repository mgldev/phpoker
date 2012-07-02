var Poker = new Class({

    connection: null,
	members: [],

    Implements: [Options, Events],

    join: function(username) {

		this.username = username;
        this.connection = new WebSocket("ws://192.168.1.100:8000");
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

		var request = {
            action: 'join',
            params: { username: this.username }
        };

        this.connection.send(JSON.encode(request));
		$.mobile.changePage('#main', { transition: "slideup" });
    },

    onmessage: function(evt) {

        var response = JSON.decode(evt.data);

		switch (response.action) {

			case 'member-list':
				this.members = response.params.members;
				this.refreshMemberList();
				break;

			case 'new-member':
				this.members.include(response.params.member);
				this.refreshMemberList();
		}
    },

    onclose: function() {

        this.setConnectedStatus(false);
		$.mobile.changePage('#connect', { transition: "slideup" });
    },

	refreshMemberList: function() {

		$('#member-list').empty();

		for (var i = 0; i < this.members.length; i++) {
			var member = this.members[i];
			$('#member-list').append('<li>' + member.displayName + '</li>');
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