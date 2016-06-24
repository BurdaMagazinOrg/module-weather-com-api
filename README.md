# Weather.com API module
 
## About
 The Weather Company is one of the leading weather data providers. This module implements certain endpoints of 
 the weather.com api and provides a basic configureable forecast block. It does not implement Wunderground Weather API.
     
## Requirements
 
 - an API-Key provided by weather.com with access to:
    - the forecast service
    - the location service
    - the observation service (current weather)

## Features

 The module provides per default a configureable block which allows to display the current weather and forecast weather 
 based on the user location. The default block also allows users to search for cities per autocomplete. 
 The autocomplete feature is provided by the free WundergroundAutocomplete API. It is possible to limit the autocomplete
 lookup per country.
 Weather data will be cached (not configureable) and user location will be stored in a cookie (not configureable).
 
## Configuration

### General configuration
 
 The general configuration can be found at "Configuration" > "Web Services" > Weather.com API Settings. You can configure 
 the API Key, the language of the weather data (measures automatically included) and the autocomplete lookup countries
 for the block widget.
 
### Forecast block configuration
 
#### Block placement
 
 Place the forecast block in any region you would like. 
 
#### Default location
 
 Enter a default location of the weather. This location will be
 used when the users location can not be located.
 
#### Data feature
 
 You can either just print the current weather (observations) or print forecast weather with optional current weather.
 

 
