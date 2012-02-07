/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('Monitor.view.upstreamError.Show', {
  extend: 'Ext.window.Window',
  alias : 'widget.upstreamerrorshow',

  requires: ['Ext.form.Panel'],

  title : 'Show error',
  layout: 'fit',
  autoShow: true,
  height: 280,
  width: 800,

  initComponent: function() {
    this.items = [
    {
      xtype: 'form',
      padding: '5 5 0 5',
      border: false,
      style: 'background-color: #fff;',

      items: [
      {
        xtype: 'textareafield',
        name : 'msg',
        fieldLabel: 'Message',
        width: 750,
        height: 60
      },
      {
        xtype: 'textfield',
        name : 'upstream',
        fieldLabel: 'Upstream',
        width: 280
      },
      {
        xtype: 'textfield',
        name : 'request',
        fieldLabel: 'Request',
        width: 750
      },
      {
        xtype: 'textfield',
        name : 'referrer',
        fieldLabel: 'Referrer',
        width: 750
      },
      {
        xtype: 'textfield',
        name : 'host',
        fieldLabel: 'Host',
        width: 300
      },
      {
        xtype: 'textfield',
        name : 'client',
        fieldLabel: 'Client',
        width: 220
      }
      ]
    }
    ];

    this.buttons = [
    {
      text: 'Close',
      scope: this,
      handler: this.close
    }
    ];

    this.callParent(arguments);
  }
});


