(function ($) {
  $("#shipping_postcode").keyup(function () {
    var zip = $("#shipping_postcode").val();

    zipCount = zip.length;
    if (zipCount == 4 && zip.charAt(3) != "-") {
      console.log(params.hyphen_text);
      $("#shipping_postcode").val(zip.substr(0, 3) + "-" + zip.charAt(3));
    } else if (zipCount > 7) {
      var url = "https://map.yahooapis.jp/search/zip/V1/zipCodeSearch";
      var param = {
        appid: params.yahoo_app_id,
        output: "json",
        query: zip,
      };
      $.ajax({
        url: url,
        data: param,
        dataType: "jsonp",
        success: function (result) {
          var ydf = new Y.YDF(result);
          if (ydf.result.count === 0) {
            resetAddress();
          } else {
            dispZipToAddress(ydf);
          }
        },
        error: function () {},
      });
    }
  });

  function dispZipToAddress(ydf) {
    var address = ydf.features[0].property.Address;
    var state = address.substr(0, 3);
    var states = new Array();

    const state_id = Object.keys(params.states_filtered).find(
      (key) => params.states_filtered[key] === state
    );
    // var state_id = params.states_filtered.indexOf(state);
    jQuery("#shipping_state").val(state_id);

    var text_num = 3;
    if (state_id == "14" || state_id == "30" || state_id == "46") {
      text_num = 4;
    }

    var city = address.substr(text_num);
    jQuery("#shipping_city").val(city);
    states[14] = params.states_jp["JP14"];
    states[30] = params.states_jp["JP30"];
    states[46] = params.states_jp["JP46"];
    if (state_id > 9) {
      document.getElementById("shipping_state").value = "JP" + state_id;
    } else {
      document.getElementById("shipping_state").value = "JP0" + state_id;
    }

    document.getElementById(params.state_element_id).innerHTML =
      params.states_filtered[state_id];
  }

  function resetAddress() {
    jQuery("#shipping_state").val(-1);
    jQuery("#shipping_city").val("");
    document.getElementById("shipping_state").selectedIndex = -1;
    document.getElementById(params.state_element_id).innerHTML = "";
  }
})(jQuery);
