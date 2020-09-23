# Crime Mapper
![Status](https://img.shields.io/website?down_color=red&down_message=offline&up_color=brightgreen&up_message=online&url=http%3A%2F%2Fec2-35-178-182-176.eu-west-2.compute.amazonaws.com)
![Commit](https://img.shields.io/github/last-commit/DanielPitfield/Crime_Mapper)
![Issues](https://img.shields.io/github/issues-raw/DanielPitfield/Crime_Mapper)

A web-based crime mapping, visualisation and analysis solution (**[Live Demo](http://ec2-35-178-182-176.eu-west-2.compute.amazonaws.com)**)

## Table of contents
* [Programming Languages](#ProgrammingLanguages)
* [Built with](#Builtwith)
	
## Programming Languages
* HTML
* CSS
* JavaScript
* PHP
* SQL

## Built with
* Google Maps JavaScript API
* MySQL
* BootStrap (v4.4.1)
* jQuery (v3.4.1)

### Add Crime

**1)** Right click anywhere on the map and select the 'Add Crime' option (from the context menu which appears):

![Add_Context_Menu](documentation/images/Add/Add_Context_Menu.PNG)

**2)** Enter the crime's information using the input fields:

![Add_Modal](documentation/images/Add/Add_Modal.PNG)

*(**Note:** The crime location can be adjusted using the marker and smaller map provided)*

**3)** Click the 'Confirm' button for the crime to be added to the mapper:

![Add_Marker](documentation/images/Add/Add_Marker.PNG)

#### Crime categories
| Icon | Description | Icon | Description |
|--|--|--|--|
| ![Violence](crime_icons/violence.png) | Violence against the person | ![Weapons](crime_icons/weapons.png) | Possession of weapons |
| ![Public Order](crime_icons/public_order.png) | Public Order | ![Theft](crime_icons/theft.png) | Theft |
| ![Drugs](crime_icons/drugs.png) | Drug offences | ![Burglary](crime_icons/burglary.png) | Burglary |
| ![Vehicle](crime_icons/vehicle.png)  | Vehicle offences | ![Robbery](crime_icons/robbery.png) | Robbery |
| ![Sexual](crime_icons/sexual.png) | Sexual offences | ![Misc](crime_icons/other.png) | Miscellaneous crimes | 
| ![Arson](crime_icons/arson.png) | Arson and criminal damage | ![Other](crime_icons/other.png) | Other |

### Known Issues ###  
* IE11 is not supported
* Not responsive for mobile devices (mobile version in development)
* Larger resolutions (above 1920x1080) are untested
