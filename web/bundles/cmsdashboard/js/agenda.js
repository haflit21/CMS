var clicked = false;	
	$(function() {
var month = $('h4.month-agenda').attr('data-month');
		var year = $('h4.month-agenda').attr('data-year');
		$('table.calendar tr.selectable').selectable({
			stop: function() {
                var result = "";
                var i=0;
                var debut=0;
                var fin=0;
                var nb_jours = $( ".ui-selected").size();
                $( ".ui-selected", this ).each(function() {
                	console.log(nb_jours+" "+i+" "+$(this).html());
                    if(i==0)
                    	debut = $(this).html();
                    if(i==nb_jours-1)
                    	fin = $(this).html();
                    i++;
                });
                console.log(fin);	
                debut = year+"-"+month+"-"+debut;
                fin = year+"-"+month+"-"+fin;
                console.log(debut+" "+fin);
                $('#event_date_debut').attr('value',debut);
                $('#event_date_fin').attr('value',fin);
                $('#myModal').modal();
            }
		});
	})