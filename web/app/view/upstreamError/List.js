/*
 * This file is part of the WebUIMonitor package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Ext.define('Monitor.view.upstreamError.List' ,{
  extend: 'Ext.grid.Panel',
  alias : 'widget.upstreamerrorlist',

  title : 'Upstream Errors',

  store : 'UpstreamError',
    
  initComponent: function(){
    Ext.apply(this, {
    
      columns: [
      {
        header: 'Date',  
        dataIndex: 'date',  
        width: 120
      },

      {
        header: 'Source',
        dataIndex: 'name',
        width: 150
      },

      {
        header: 'Severity', 
        dataIndex: 'error_severity',  
        width: 50, 
        align: 'center',
        renderer: this.formatSeverity
      },

      {
        header: 'Type', 
        dataIndex: 'error_type',  
        width: 50,
        align: 'center',
        renderer: this.formatType
      },

      {
        header: 'Message', 
        dataIndex: 'msg', 
        flex: 1,
        renderer: this.formatTooltip
      },

      {
        header: 'Upstream', 
        dataIndex: 'upstream',  
        width: 140,
        renderer: this.formatUpstream        
      },

      {
        header: 'Request', 
        dataIndex: 'request',  
        width: 250,
        renderer: this.formatTooltip        
      }
      ],
      
      bbar: Ext.create('Ext.ux.StatusBar', {
        id: 'basic-statusbar',

        // defaults to use when the status is cleared:
        defaultText: 'Not connected',
        //defaultIconCls: 'default-icon',
        
        // values to set initially:
        text: 'Not connected',
        iconCls: 'x-status-error',

        // any standard Toolbar items:
        items: [
          'Upstream errors'
        ]
      })      
      
    });
    this.callParent();
  },
    
  formatSeverity: function(value, p, record) {
    if(value == 0)
    {
        return '';
    }
    if(value == 1)
    {
      var path_img = 'images/fam/critical.gif';
      var alt = 'Critical';
    }
    if(value == 2)
    {
      var path_img = 'images/fam/warning.gif';
      var alt = 'Warning';
    }
    if(value == 3)
    {
      var path_img = 'images/fam/information.png';
      var alt = 'Info';
    }
    p.tdAttr = 'data-qtip=" ' + alt + '"' ;
    return Ext.String.format('<img src="{0}" alt="{1}">', path_img, alt);
  },    
    
  formatType: function(value, p, record) {
    if(value == 0)
    {
      return '';
    }
    if(value == 1)
    {
      var path_img = 'images/fam/fatal.gif';
      var alt = 'Fatal';
    }
    if(value == 2)
    {
      var path_img = 'images/fam/syntax_error.png';
      var alt = 'Syntax';
    }
    if(value == 3)
    {
      var path_img = 'images/fam/sql.gif';
      var alt = 'Sql';
    }
    if(value == 4)
    {
      var path_img = 'images/fam/sf.png';
      var alt = 'Symfony';
    }
    if(value == 5)
    {
      var path_img = 'images/fam/information.png';
      var alt = 'Information';
    }
    p.tdAttr = 'data-qtip=" ' + alt + '"' ;
    return Ext.String.format('<img src="{0}" alt="{1}">', path_img, alt);
  },
  
  formatUpstream: function(value, p, record) {
    this.formatTooltip(value, p, record);
    value = value.replace(/fastcgi:\/\//, '');
    return value;
  },
  
  formatTooltip: function(value, p, record) {
    p.tdAttr = 'data-qtip=" ' + Ext.String.htmlEncode(value) + '"' ;
    return value;
  }    
});

