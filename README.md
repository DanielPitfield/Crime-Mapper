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

## Usage / Instructions
### Crime Icons
| Icon | Description | Icon | Description |
|--|--|--|--|
| ![Violence](crime_icons/violence.png) | Violence against the person | ![Weapons](crime_icons/weapons.png) | Possession of weapons |
| ![Public Order](crime_icons/public_order.png) | Public Order | ![Theft](crime_icons/theft.png) | Theft |
| ![Drugs](crime_icons/drugs.png) | Drug offences | ![Burglary](crime_icons/burglary.png) | Burglary |
| ![Vehicle](crime_icons/vehicle.png)  | Vehicle offences | ![Robbery](crime_icons/robbery.png) | Robbery |
| ![Sexual](crime_icons/sexual.png) | Sexual offences | ![Misc](crime_icons/other.png) | Miscellaneous crimes | 
| ![Arson](crime_icons/arson.png) | Arson and criminal damage | ![Other](crime_icons/other.png) | Other |

### Add Crime

**1)** Right click anywhere on the map and select the 'Add Crime' option (from the context menu which appears):

![Add_Context_Menu](documentation/images/Add/Add_Context_Menu.PNG)

**2)** Enter the crime's information using the input fields:

*(**Note**: The crime location can be adjusted using the marker and smaller map provided)*

![Add_Modal](documentation/images/Add/Add_Modal.PNG)

**3)** Click the 'Confirm' button for the crime to be added to the mapper:

![Add_Marker](documentation/images/Add/Add_Marker.PNG)

### View Crime

**1)** Left clicking on a marker will open a small window displaying information about the crime (current properties):

![View](documentation/images/View/View_Marker.PNG)

### Edit Crime

**1)** From the window that appears when viewing a crime, click the 'Edit' button:

![Edit](documentation/images/Edit/Edit_Button.PNG)

**2)** Edit the crime's information using the input fields:

*(**Note:** The inputs fields will begin as to reflect the crime's current information)*

![Edit_Modal](documentation/images/Edit/Edit_Modal.PNG)

**3)** Click the 'Confirm' button to save the edits made.

### Delete Crime

**1)** To remove a single instance of crime, from the window that appears when viewing that crime, click the 'Delete' button:

(**Note:** This will **permanently remove** the crime marker from the mapper).

![Delete](documentation/images/Delete/Delete_Button.PNG)

### Filter Crime

**1)** From the main toolbar, click the 'Filter Crime' button:

![Filter](documentation/images/Filter/Filter_Button.PNG)

**2)** Enter the desired filter criteria with the provided fields:

*(**Note:** Where there are two fields for a crime attribute, the first field is a minimum value and the second field is a maximum value)*

![Filter_Modal](documentation/images/Filter/Filter_Modal.PNG)

* **2.1)** To include a geographic area in the filter criteria, first click on the smaller map provided:

![Filter_Map](documentation/images/Filter/Filter_Map.PNG)

* **2.2)** This will enable the 'Search Radius (miles) field'. Selecting a radius from this list will display the area to filter by on the map:

![Filter_Radius](documentation/images/Filter/Filter_Radius.PNG)

**3)** Click the 'Confirm' button to filter all markers by the constructed filter criteria.

#### Clear Filter

**1)** Click the 'Filter Crime' button at the top of the window:

**2)** (Optional) Click the 'Confirm' button to show all markers (apply no filter).

#### Filter by ID

**1)** Enter an ID in the field at the top of the window:

![Filter_ID](documentation/images/Filter/Filter_ID.PNG)

**2)** Click the search button (beside the input field).

#### Delete Filtered Crime

**1)** All the crimes that meet the currently active filter criteria can be deleted all at once by clicking the 'Delete Filtered (Visible) Markers' button:

**2)** A progress bar showing the deletion process will appear:

**3)** Once the process has finished, the mapper will reload (and the deletions made will be reflected on the mapper).

### Import Crime

**1)** From the main toolbar, click the 'Import Crime' button:

### Analyse Crime

**1)** From the main toolbar, click the 'Analyse Crime' button:

**2)** All currently visible markers will be grouped into differently coloured clusters based on how close they are to other markers (represents the density of crime). The number of crimes in each cluster is shown with white text in the middle of the cluster icon. The three different cluster icons and the minimum number of crimes they represent is as follows:
| Cluster | Amount |
|--|--|
| ![Violence](cluster_images/SmallCluster.png) | 2 - 10 |
| ![Violence](cluster_images/MediumCluster.png) | 11 - 99 |
| ![Violence](cluster_images/LargeCluster.png) | 100+ |

**3)** To turn off the clustering, click the 'Analyse Crime' button again (the button acts as a toggle). 

### Predict Crime

Crime prediciotn functionality is not yet implemented. This feature will be added soon.

### Known Issues ###  
* IE11 is not supported
* Not responsive for mobile devices (mobile version in development)
* Larger resolutions (above 1920x1080) are untested
* Import and multiple marker deletion functionality not performed