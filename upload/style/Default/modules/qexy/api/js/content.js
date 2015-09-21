$(function(){
	$("[rel='tooltip']").tooltip({container: 'body'});

	$("body").on('click', '.api-del-accept', function(){
		return confirm("Вы уверены, что хотите удалить выбранный элемент?");
	});

	$('body').on('click', '.api-bb-panel a[href="#bb"]', function(){
		var panel_for = $(this).closest('.api-bb-panel').attr('data-for');
		var panel_obj = $(panel_for)[0];

		var leftcode = $(this).attr('data-left');
		var rightcode = ($(this).attr("data-right")==undefined) ? leftcode : $(this).attr("data-right");

		
		if(!$(this).hasClass("woborder")){
			leftcode = '['+leftcode+']';
			rightcode = (rightcode=='') ? '' : '[/'+rightcode+']';
		}else{
			rightcode = (rightcode=='') ? '' : rightcode;
		}

		if(document.selection){

			var s = document.selection.createRange();
			if(s.text){
				s.text = leftcode + s.text + rightcode;
			}

		}else{ // Opera, FireFox, Chrome

			var start = (panel_obj.selectionStart==undefined) ? 0 : panel_obj.selectionStart;

			var end = (panel_obj.selectionEnd==undefined) ? 0 : panel_obj.selectionEnd;

			s = panel_obj.value.substr(start,end-start);

			panel_obj.value = panel_obj.value.substr(0, start) + leftcode + s + rightcode + panel_obj.value.substr(end);
		}

		return false;
	});

	$('.api-spl').hide();

	$('body').on('click', '.api-spl-target', function(){

		var id = $(this).attr('data-for');

		$('.api-spl'+id).toggleClass("opened").slideToggle('fast');

		return false;
	});

	$('body').on('click', '.api-check-all', function(){
		var for_elem = $(this).attr('data-for');

		if($(this)[0].checked){
			$('input[type="checkbox"].'+for_elem).prop('checked', true);
		}else{
			$('input[type="checkbox"].'+for_elem).prop('checked', false);
		}
	});
});