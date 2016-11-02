
/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview Definition for placeholder plugin dialog.
 *
 */

'use strict';

CKEDITOR.dialog.add( 'placeholder', function( editor ) {
	var lang = editor.lang.placeholder,
		generalLabel = editor.lang.common.generalTab,
		validNameRegex = /^[^\[\]<>]+$/;
                
        var items = [];
            /*
                $.ajax({
                   url : 'http://localhost/clarituscore/web/index.php/adminuser/admin/getplaceholders',
                   type:'post',
                   data:{
                       
                   },
                   success:function(status) {
                        var parsedData = JSON.parse(status);
                        alert(parsedData);
                        if(parsedData.CODE == 200){
                            items = parsedData.DATA;
                        } else if(parsedData.CODE == 100) {
                            items  = [ [ 'FIRSTNAME' ], [ 'LASTNAME' ], ['LINK'] ];
                        }
                    }
                });
                */
                

	return {
		title: lang.title,
		minWidth: 300,
		minHeight: 80,
		contents: [
			{
				id: 'info',
				label: generalLabel,
				title: generalLabel,
				elements: [
					// Dialog window UI elements.
					{
						id: 'name',
						//type: 'text',
						style: 'width: 100%;',
						label: lang.name,
						
                                                type : 'select',
                                                items : [ 
                                                            
                                                            [ 'USER.FIRSTNAME' ], 
                                                            [ 'USER.LASTNAME' ], 
                                                            [ 'USER.STATUS' ], 
                                                            [ 'SUBSCRIBER.FIRSTNAME' ], 
                                                            [ 'SUBSCRIBER.LASTNAME' ], 
                                                            [ 'SUBSCRIBER.STATUS' ], 
                                                            ['LINK'], 
                                                            ['NAME'], 
                                                            ['TYPE'], 
                                                            ['VENDOR'], 
                                                            ['DEPARTMENT'], 
                                                            ['UTR'], 
                                                            ['STATUS'], 
                                                            ['TOKEN'], 
                                                            ['DATE'],
                                                            ['OTHER1'],
                                                            ['OTHER2'],
                                                            ['OTHER3']
                                                        ],
                                                
                                                'default' : '',
                                                
						required: true,
						validate: CKEDITOR.dialog.validate.regex( validNameRegex, lang.invalidName ),
						setup: function( widget ) {
							this.setValue( widget.data.name );
						},
						commit: function( widget ) {
							widget.setData( 'name', this.getValue() );
						}
					}
				]
			}
		]
	};
} );
