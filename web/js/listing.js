/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function() {
    $('#user-listing').DataTable();    
});



//display responsive no-wrap

$(document).ready(function() {
    var langArr = {'ar': 'Arabic', 'en': 'English', 'hi': 'Hindi'};
    var url = 'http://cdn.datatables.net/plug-ins/1.10.7/i18n/English.json';
    var language = getCookie('language');
    
    if(language != ''){
        if(langArr[language]){
            url = 'http://cdn.datatables.net/plug-ins/1.10.7/i18n/'+langArr[language]+'.json';
        }
    }
        
    //var table = $('#permission-list').DataTable( {
    var table = $('#listing-table').DataTable( {        
        "order": [[ 1, "desc" ]],
        "language": {
            "url": url
        }
        
        
        /*
        language: {
            processing:     "Traitement en cours...",
            search:         "Rechercher&nbsp;:",
            lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
            info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix:    "",
            loadingRecords: "Chargement en cours...",
            zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable:     "Aucune donnée disponible dans le tableau",
            paginate: {
                first:      "Premier",
                previous:   "Pr&eacute;c&eacute;dent",
                next:       "Suivant",
                last:       "Dernier"
            },
            aria: {
                sortAscending:  ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }
        */
    } );
    
    
    

   // Handle click on "Select all" control
   $('#permission-list-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = table.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });
   
});

$(document).ready(function() {
    
    if (window.matchMedia('(max-width: 979px)').matches) {
        var table=$('#lookuptype-listing').DataTable({
        "responsive":true,
        "columnDefs": [
            {
                "targets": [ 1 ],
                "visible": false
            },
            {
                "targets": [ 3 ],
                "visible": false
            },
            {
                "targets": [ 4 ],
                "visible": false
            },
            {
                "targets": [ 5 ],
                "visible": false
            },
            {
                "targets": [ 6 ],
                "visible": false
            }
        ]
    });
    }
    else {
        var table=$('#lookuptype-listing').DataTable({
            "responsive":true
        });
    }

});

$(document).ready(function() {
    $('#lookup-listing').DataTable();
});

$(document).ready(function() {
    $('#country-listing').DataTable();
});

$(document).ready(function() {
    $('#state-listing').DataTable();
});

$(document).ready(function() {
    $('#city-listing').DataTable();
});


$(document).ready(function() {
    $('#logs-listing').DataTable({
        "order": [[ 1, "desc" ]],
        
    } );
});

$(document).ready(function() {
    $('#device-listing').DataTable();
});



