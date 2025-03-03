$(".basic").select2({
	tags: true,
});

var formSmall = $(".form-small").select2({ tags: true });

formSmall.on('select2:select', function(e) {
$(this).data('select2').$container.addClass('form-control-sm');
formSmall.off('select2:select'); //remove the event listener
});

formSmall.on('select2:open', function(e) {
	$(this).data('select2').$container.addClass('form-control-sm');
});

$(".nested").select2({
	tags: true
});
$(".tagging").select2({
	tags: true
});
$(".disabled-results").select2();
$(".placeholder").select2({
	placeholder: "Make a Selection",
	allowClear: true
});

function formatState (state) {
  if (!state.id) {
	return state.text;
  }
  var baseClass = "flaticon-";
  var $state = $(
	'<span><i class="' + baseClass + state.element.value.toLowerCase() + '" /> ' + state.text + '</i> </span>'
  );
  return $state;
};

$(".templating").select2({
  templateSelection: formatState
});