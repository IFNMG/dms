//to select-deselect all
$(".device-multicheck").click(function(){
    if($('.device-multicheck:checked').length) { 
        $("#sendactions").show();
    }else{$("#sendactions").hide();}

}); 


function loadConfigChild(s,id,url,disableStatus){
    //console.log('LCC'+s+':'+id+':'+url+':'+disableStatus);
    var val="";
   // val=s[s.selectedIndex].id;
    val=s;
    
    if(val!=""){
        $.ajax({
                type:'post',
                data:{
                    section_id: val,                
                },
                url : url,
                success:function(data) {    
                    if(data!=""){
                        $("#child_"+id).html(data);
                        if(disableStatus=='TRUE'){
                            $("#panel_"+id).find(".myinput").attr("disabled","true");    
                        }
                    }
                 }
             });
    }
}

function generateKey(id,type,url){
    
    if(id!="" && type!=""){
        $.ajax({
                type:'post',
                data:{
                    id: id,                
                    type: type,                
                },
                url : url,
                success:function(data) {    
                    if(data!=false){
                        $("#val_"+id).val(data);
                    }else{
                        $("#config-error").text('There is some error.Please try again.');       
                        $("#config-error-box").css("display","block");
                    }
                 }
             });
    }
}
/**
 * @author Prachi
 * @description To save config 
 * */
function save(id,value,url,reloadurl,loadChildUrl){    
    //console.log('Save'+id+':'+value);
    if(id!=""){
        
        var Contain = {};
        var key="";
        $("#panel_"+id+" :text").each(function(){  
            key="";
            key=$(this).attr('id');
            key = key.replace('val_', '');
            Contain[key]=$(this).val();            
        });
        
        $("#panel_"+id+" select").each(function(){   
            key="";
            key=$(this).attr('id');
            key = key.replace('val_', '');
            Contain[key]=$(this).val();          
        });
        
        $.ajax({
                type:'post',
                data:{
                    parent_id: id,                                    
                    contain:Contain,
                },
                url : url,
                success:function(data) {    
                    if(data==true){
                        
                        reloadPanel(id,value,reloadurl,loadChildUrl);                        
                        $("#config-success").text('Configurations has been updated successfully.');       
                        $("#config-success-box").css("display","block");
                        
                    }else{
                        $("#config-error").text('There is some error.Please try again.');       
                        $("#config-error-box").css("display","block");
                    }
                 }
             });
    }
}

function showEditLink(id){            
   if($('#functional_'+id).css('display') == 'block'){
        $("#edit_"+id).hide();    
   }else{
        $("#edit_"+id).show();    
   }
 }

function hideEditLink(id){            
   if($('#functional_'+id).css('display') == 'none'){
        $("#edit_"+id).hide();    
   }else{
        $("#edit_"+id).hide();    
   }
 }

function showFunctional(id){
    // remove disable from all inputs    
    $("#functional_"+id).show();
    $("#edit_"+id).hide();
    $("#panel_"+id).find(".myinput").removeAttr("disabled");
    
}

function hideFunctional(id){
    //alert('hi');
    $("#functional_"+id).hide();
    //$("#functional_"+id).find('a').hide();
}

function reloadPanel(id,value,url,loadChildUrl){
    //console.log('RP'+id+':'+value+':'+url+':'+loadChildUrl);
    // add disable from all inputs    
    $("#functional_"+id).hide();
    $("#edit_"+id).show();
    $("#panel_"+id).find(".myinput").attr("disabled","true");
    //refresh value get value from db
    
    if(id!=""){
        $.ajax({
                type:'post',
                data:{
                    parent_id: id,                                    
                },
                url : url,
                success:function(data) {                       
                    if(data){
                        var data=JSON.parse(data);
                        $("#panel_"+id+" :text").each(function(){  
                            key="";
                            key=$(this).attr('id');
                            var keyId = key.replace('val_', '');                                                        
                            $("#val_"+keyId).val(data[keyId]);
                        });

                        $("#panel_"+id+" select").each(function(){   
                            key="";
                            key=$(this).attr('id');
                            key = key.replace('val_', '');
                            var keyId = key.replace('val_', '');     
                            $("#val_"+keyId).val(data[keyId]);
                        });
                        
                        loadConfigChild(value,id,loadChildUrl,'TRUE');
                        
                        
                        
                    }else{
                        $("#config-error").text('There is some error.Please try again.');       
                        $("#config-error-box").css("display","block");
                    }
                 }
             });
             
             
             
              
        var Contain = {};
        var key="";
        $("#panel_"+id+" :text").each(function(){  
            key="";
            key=$(this).attr('id');
            key = key.replace('val_', '');
            Contain[key]=$(this).val();            
        });
        
        $("#panel_"+id+" select").each(function(){   
            key="";
            key=$(this).attr('id');
            key = key.replace('val_', '');
            Contain[key]=$(this).val();          
        });
             
    }
    
    
    
    
    
    
}


function showfilterlist(){
    $("#device-filter-icon").hide();
    $("#device-filter-list").show(1000);
    
}


function hidefilterlist(){    
    $("#device-filter-list").hide(1000);
    $("#device-filter-icon").show();
    
}

function openNotificationBox(){
    
    
}


function hideCustomAlertBox(box_id,textbox_id){
    $("#"+box_id).hide();
    $("#"+textbox_id).text('');
}

