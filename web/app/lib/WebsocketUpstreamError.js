/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('Monitor.lib.WebsocketUpstreamError', {
    extend: 'Monitor.lib.Websocket',

    config: {
        url: null,
        linesLimit: 1000,
        scope: this
    },

    playing_alarm: false,
    statusBar: null,

    constructor: function(config) {
        this.initConfig(config);

        var configParent = {
            url: this.url,
            scope: this.scope
        };

        this.callParent([configParent]);

        this.statusBar = Ext.getCmp('basic-statusbar');

        return this;
    },

    supervisor: function() {
        console.log('supervisor started...');
        var task = {
            run: function() {

                var readyState = this.wsocket.readyState;

                // waiting for first message
                if (Ext.isGecko) var connecting = MozWebSocket.CONNECTING;
                else var connecting = WebSocket.CONNECTING;
                if (readyState == connecting) {
                    this.statusBar.setStatus({
                        text: 'Waiting for first message...',
                        iconCls: 'x-status-busy'
                    });
                }

                // open
                if (Ext.isGecko) var open = MozWebSocket.OPEN;
                else var open = WebSocket.OPEN;
                if (readyState == open) {
                    this.statusBar.setStatus({
                        text: 'Connected',
                        iconCls: 'x-status-valid'
                    });
                }

                // closed
                if (Ext.isGecko) var closed = MozWebSocket.CLOSED;
                else var closed = WebSocket.CLOSED;
                if (readyState == closed) {
                    this.statusBar.setStatus({
                        text: 'Not connected',
                        iconCls: 'x-status-error'
                    });
                    console.log('reconnecting websocket...');
                    this.start();
                }

            },
            interval: 1000, //1 second
            scope: this
        }
        Ext.TaskManager.start(task);
    },

    onMessage: function(msg) {
        // note: this => is WebSocket scope
        this.scope.callParent([msg]);

        var data = Ext.JSON.decode(msg.data);
        if (typeof(data.msg) == 'undefined' && typeof(data.event) == 'undefined') return; // no data

        // insert to grid
        var modelUpstreamError = this.scope.scope.getModel('UpstreamError');
        var storeUpstreamError = this.scope.scope.getUpstreamErrorStore();

        // event || alarm
        if(typeof(data.event) != 'undefined')
        {
            // event
            var t = new modelUpstreamError({
                date: data.ts,
                msg: data.event,
                error_type: 5,
                error_severity: 0
            });
            // insert on top
            storeUpstreamError.insert(0, t);
        }
        else
        {
            // alarm
            var t = new modelUpstreamError({
                date: data.date,
                name: data.name,
                msg: data.msg,
                error_type: data.error_type,
                error_severity: data.error_severity,
                upstream: data.upstream,
                request: data.request,
                referrer: data.referrer,
                server: data.server,
                host: data.host,
                client: data.client
            });
            // insert on top
            storeUpstreamError.insert(0, t);
//    storeUpstreamError.add(t);
            //storeUpstreamError.sort('date', 'DESC');

            // raise up alarm
            this.scope.raiseUpAlarm(t, this.scope);
        }

        // autoclean
        this.scope.autoClean(storeUpstreamError);
    },

    raiseUpAlarm: function(upstream_error, scope) {
        // play alarm sound once and wait N minutes to replay if is necessary
        if (scope.playing_alarm) return;

        scope.playing_alarm = true;

        t = setTimeout(function() {
            scope.playing_alarm = false;
        }, 60000 * 2); // 2 minutes

        // play sound
        console.log('playing alarm sound...');
        scope.playAlarmSound(upstream_error);
    },

    playAlarmSound: function(upstream_error) {
        var snd = false;
        if (upstream_error.data.error_severity == 1) var snd = new Audio('sounds/severity/error_critic.wav');
        if (upstream_error.data.error_severity == 2) var snd = new Audio('sounds/severity/error_generic.wav');
        if (snd)
        {
            snd.addEventListener('ended', function(){
                if(upstream_error.data.host == 'backoffice.myweb.com') var snd2 = new Audio('sounds/host/error_backoffice.wav');
                if(upstream_error.data.host == 'www.myweb.com') var snd2 = new Audio('sounds/host/error_frontal.wav');
                if(typeof(snd2) != 'undefined') snd2.play();
            });
            snd.play();
        }
    },

    autoClean: function(store) {
        var c;
        while (c = store.count() >= this.linesLimit) {
            store.removeAt(store.indexOf(store.last()));
        }

    }

});
