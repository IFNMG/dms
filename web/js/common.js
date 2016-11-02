     var table = $('#listing').DataTable( {
        "order": [[ 1, "desc" ]],
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            
         }],
    } );
    
    
    

   // Handle click on "Select all" control
   $('#listing-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = table.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });

   // Handle click on checkbox to set state of "Select all" control
   $('#listing tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#listing-select-all').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control 
            // as 'indeterminate'
            el.indeterminate = true;
         }
      }
   });

  
    function getFilteredList(url, selectId, e){
        var id = e.value;
        $('#'+selectId).html('<option value="">-- Select --</option>');
        if(id != ''){
            $.ajax({
                type:'post',
                data:{
                    id: id,
                },
                url : url,
                success:function(status) {
                    $('#'+selectId).html(status);
                }
             });
        } 
    }
  
    
   function activateDeactivate(id, url, status,entity){       
        var status = status;        
        var retVal="";       
        if(id != '' && status!==''){          
            if(entity=="dd"){
             retVal = confirm("Do you want to continue ?");
            }else{
                retVal=true;
            }
            if( retVal == true ){
                $.ajax({
                   type:'post',
                   data:{
                       id: id,
                       status: status
                   },
                   url : url,
                   success:function(status) {
                        url = "'"+url+"'";
                        var parsedData = JSON.parse(status);
                        
                        if(parsedData.CODE == 200){
                            $('#status_'+id).html(parsedData.DATA);
                          //  alert('change successfully');
                        } else if(parsedData.CODE == 100) {
                            //alert(parsedData.MESSAGE);
                        }
                    }
                });
            }
          // window.location.reload();
       }
    }
    
    
    
    function permanentDelete(id, url, e){
        
        if(id != ''){
            var url = url; 
            var retVal = confirm("Do you want to continue ?");
            if( retVal == true ){
                $.ajax({
                    type:'post',
                    data:{
                        id: id,
                        
                    },
                    url:url,
                    success:function(status) {
                        var table = $('#listing-table').DataTable();
                        $('#viewList').trigger('click');

                        var parsedData = JSON.parse(status);                       
                        if(parsedData.CODE == 200){                            
                            $('#tr_'+e).parents('tr').remove();
                            table
                                .row( $(e).parents('tr') )
                                .remove()
                                .draw();
                        } else if(parsedData.CODE == 100) {
                            alert(parsedData.MESSAGE);
                        }
                    }
                });
              
            } 
            
            
       }   
    }
    
    function setCookie(key, value) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
    }

    function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

   function createSlug(source,destination,prefix) {
        var slug="";
        var prefixval="";
        source = document.getElementById(source);        
        destination = document.getElementById(destination);
        if(prefix){prefixval=prefix;}
        slug=prefixval+source.value;
        slug = slug.replace(/ /g,"_");
        slug = slug.replace(/-/g,"_");
        slug = slug.replace(/[^a-zA-Z0-9_]/g,'');
        slug=slug.toUpperCase();
        destination.value=slug;
    }
    
    function checkSlugExist(slugSource,url){
      var slug  ="";
      slug=$("#"+slugSource).val();
      if(slug!="" && url!=""){
        $.ajax({
              type:'post',
              data:{
                  slug: slug                                          
              },
              url : url,
              success:function(shortCode) {    
                  if(shortCode!=""){
                      $("#"+slugSource).val(shortCode);
                  }
               }
           });   
       }
    }