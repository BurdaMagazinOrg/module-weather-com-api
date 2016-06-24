/**
 * Created by luciowassill on 26.05.16.
 */
(function($) {
    Drupal.AjaxCommands.prototype.replaceWeatherWidget = function(ajax, response, status, location){
        $(response.selector).each(function() {
            $(this).replaceWith(response.data);
            $('#weather-widget-city').val(response.location.city);
            $('#weather-widget-coordinates').val(response.location.coordinates);
        });
    }
    Drupal.behaviors.weather_api = {
        attach: function(context, settings) {
            // If geolocation feature is available and coordinates are empty, check the position of the  user.
            if (navigator.geolocation ) {
                if ($('#weather-widget-coordinates').val() == "" ) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        // Double check that no coordinates are there since looking up the position might take a while
                        // and coordinates might be filled in by the city we stored in the cookie.
                        if ($('#weather-widget-coordinates').val() == "" ) {
                            $('#weather-widget-coordinates').val(position.coords.latitude + ":" + position.coords.longitude);
                            $("#weather-widget-submit").trigger("mousedown");
                            $("#weather-widget-submit").trigger("mouseup");
                        }
                    }, function(error) {
                        if ($('#weather-widget-coordinates').val() == "" ) {
                            $("#weather-widget-submit").trigger("mousedown");
                            $("#weather-widget-submit").trigger("mouseup");
                        }
                    });
                }
                else {
                    // Coordinates are filled in but no city is present, fire the event
                    // May happen if a user navigates while the coordinates are filled in.
                    if ($('#weather-widget-city').val() == "" ) {
                        $("#weather-widget-submit").trigger("mousedown");
                        $("#weather-widget-submit").trigger("mouseup");
                    }
                }
            }
            else {
                // Use the default city from the block.
                if ($('#weather-widget-coordinates').val() == "" ) {
                    $("#weather-widget-submit").trigger("mousedown");
                    $("#weather-widget-submit").trigger("mouseup");
                }
            }
            // React on autocomplete selection.
            $(context).find('#weather-widget-city').autocomplete({
                select: function(event, ui) {
                    // Clear the coordinates field since the data might be old.
                    $("#weather-widget-coordinates").val("");
                    $("#weather-widget-city").val(ui.item.value);
                    $("#weather-widget-submit").trigger("mousedown");
                    $("#weather-widget-submit").trigger("mouseup");
                }
            });
        }
    }
})(jQuery);