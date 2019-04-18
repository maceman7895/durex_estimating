<!doctype html>
<html lang="en">
<?php
?>

<header>
   <style>
  h3 {
	  text-align: center;
	}

	.bootstrap-select {
	  width: 100% !important;
}
</style>
</header>

<body>
 <div class="container">
    <div class="row">
      <div class="col-xs-4">
        <h3>Without<br>Ajax-Bootstrap-Select</h3>
        <select id="selectpicker" class="selectpicker" data-live-search="true">
          <option>Mustard</option>
          <option>Ketchup</option>
          <option>Relish</option>
        </select>
      </div>

      <div class="col-xs-4">
        <h3>With<br>Ajax-Bootstrap-Select</h3>
        <select id="ajax-select" class="selectpicker with-ajax" data-live-search="true"></select>
      </div>

      <div class="col-xs-4">
        <h3>Multiple<br>Ajax-Bootstrap-Select</h3>
        <select id="ajax-select-multiple" class="selectpicker with-ajax" multiple data-live-search="true"></select>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-4">
        <h3>Cached Options<br>Ajax-Bootstrap-Select</h3>
        <select class="selectpicker with-ajax" data-live-search="true" multiple>
                <option value="neque.venenatis.lacus@neque.com" data-subtext="neque.venenatis.lacus@neque.com" selected>
                    Chancellor
                </option>
            </select>
      </div>
    </div>
  </div>
 	</body>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script>
var options = {
  values: "a, b, c",
  ajax: {
    url: "ajax.php",
    type: "POST",
    dataType: "json",
    // Use "{{{q}}}" as a placeholder and Ajax Bootstrap Select will
    // automatically replace it with the value of the search query.
    data: {
      q: "{{{q}}}"
    }
  },
  locale: {
    emptyTitle: "Select and Begin Typing"
  },
  log: 3,
  preprocessData: function(data) {
    var i,
      l = data.length,
      array = [];
    if (l) {
      for (i = 0; i < l; i++) {
        array.push(
          $.extend(true, data[i], {
            text: data[i].Name,
            value: data[i].Email,
            data: {
              subtext: data[i].Email
            }
          })
        );
      }
    }
    // You must always return a valid array when processing data. The
    // data argument passed is a clone and cannot be modified directly.
    return array;
  }
};

$(".selectpicker")
  .selectpicker()
  .filter(".with-ajax")
  .ajaxSelectPicker(options);
$("select").trigger("change");

function chooseSelectpicker(index, selectpicker) {
  $(selectpicker).val(index);
  $(selectpicker).selectpicker('refresh');
}
</script>
</html>