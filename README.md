# Crime Mapper
A web-based crime mapping, visualisation and analysis solution using the Google Maps JavaScript API

![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript)
![jQuery](https://img.shields.io/badge/jquery-%230769AD.svg?style=for-the-badge&logo=jquery)
![Google Cloud](https://img.shields.io/badge/Google%20Cloud-%234285F4.svg?style=for-the-badge&logo=google-cloud&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white&color=black)
![HTML](https://img.shields.io/badge/html-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%23563D7C.svg?style=for-the-badge&logo=bootstrap&logoColor=white)

![Home](documentation/images/Home.PNG)

## Table of Contents
* [Usage / Instructions](#usage--instructions)
    * [Crime Icons](#crime-icons)
    * [Add Crime](#add-crime)
    * [View Crime](#view-crime)
    * [Edit Crime](#edit-crime)
    * [Delete Crime](#delete-crime)
    * [Filter Crime](#filter-crime)
        * [Filter by ID](#filter-by-id)
        * [Delete Filtered Crime](#delete-filtered-crime)
    * [Import Crime](#import-crime)
    * [Analyse Crime](#analyse-crime)
        * [Cluster Icons](#cluster-icons)
* [Known Issues](#known-issues)

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

**1)** From the 'View Crime' for a crime, click the 'Edit' button:

![Edit](documentation/images/Edit/Edit_Button.png)

**2)** Edit the crime's information using the input fields:

*(**Note:** The inputs fields will begin as to reflect the crime's current information)*

![Edit_Modal](documentation/images/Edit/Edit_Modal.PNG)

**3)** Click the 'Confirm' button to save the edits made.


### Delete Crime

**1)** To remove a single instance of crime, from the 'View Crime' for a crime, click the 'Delete' button:

(**Note:** This will **permanently remove** the crime marker from the mapper).

![Delete](documentation/images/Delete/Delete_Button.png)


### Filter Crime

**1)** From the main toolbar, click the 'Filter Crime' button:

![Filter](documentation/images/Filter/Filter_Button.PNG)

**2)** Enter the desired filter criteria with the provided fields:

*(**Note:** Where there are two fields for a crime attribute, the first field is a minimum value and the second field is a maximum value)*

![Filter_Modal](documentation/images/Filter/Filter_Modal.PNG)

**2.1)** To include a geographic area in the filter criteria, first click on the smaller map provided:

![Filter_Map](documentation/images/Filter/Filter_Map.png)

**2.2)** This will enable the 'Search Radius (miles)' field. Selecting a radius will display the area to filter by on the map:

![Filter_Radius](documentation/images/Filter/Filter_Radius.png)

**3)** Click the 'Confirm' button to filter all markers by the constructed filter criteria.


#### Clear Filter

**1)** Click the 'Clear Filter' button at the top of the window:

![Filter_Clear](documentation/images/Filter/Filter_Clear.png)

**2)** (Optional) Click the 'Confirm' button to show all markers (apply no filter).


#### Filter by ID

**1)** Enter an ID in the field at the top of the window:

![Filter_ID](documentation/images/Filter/Filter_ID.png)

**2)** Click the search button (beside the input field).


#### Delete Filtered Crime

**1)** All the crimes that meet the currently active filter criteria can be deleted all at once by clicking the 'Delete Filtered (Visible) Markers' button:

![Filter_Delete](documentation/images/Filter/Filter_Delete.png)

**2)** A progress bar showing the deletion process will appear:

![Filter_Delete_Progress](documentation/images/Filter/Filter_Delete_Progress.PNG)

**3)** Once the process has finished, the mapper will reload (and the deletions made will be reflected on the mapper).


### Import Crime

**1)** From the main toolbar, click the 'Import Crime' button:

![Import](documentation/images/Import/Import_Button.PNG)

**2)** (Optional) Click the 'Download Template' button to begin creating an import file OR the 'Browse Files' to be navigated to supported data downloads:

![Import_Download](documentation/images/Import/Import_Download.png)

**3)** Click the 'Browse' button and select the file to import using the dialog:

![Import_Browse](documentation/images/Import/Import_Browse.png)

**4)** Click the 'Import' button to begin importing the selected file.

**5)** The two progress bars will update (the top bar showing progress of the file upload and the bottom bar showing the progress of the import process):

![Import_Progress](documentation/images/Import/Import_Progress.PNG)

**6)** Once the import process has finished (both progress bars complete), the mapper will reload (and the insertions made will be reflected on the mapper):

![Import_Marker](documentation/images/Import/Import_Marker.png)

### Analyse Crime

**1)** From the main toolbar, click the 'Analyse Crime' button:

![Analyse](documentation/images/Analyse/Analyse_Button.PNG)

**2)** Markers will be grouped into clusters based on how close they are to other markers (representing the density of crime). The number of crimes in each cluster is shown with white text (in the middle of the cluster icon):

![Analyse](documentation/images/Analyse/Analyse_Marker.png)

#### Cluster Icons

The three different cluster icons and the minimum number of crimes they represent is shown in the table below:
| Cluster | Amount |
|--|--|
| ![Violence](cluster_images/SmallCluster.png) | 2 - 9 |
| ![Violence](cluster_images/MediumCluster.png) | 10 - 99 |
| ![Violence](cluster_images/LargeCluster.png) | 100+ |

**3)** To turn off the clustering, click the 'Analyse Crime' button again (the button acts as a toggle).

### Predict Crime

Not yet implemented.

## Known Issues
* Database configuration details tracked within version control
* API calls (and therefore the API key) are client-side
* IE11 is not supported
* Not responsive for mobile devices
* Larger resolutions (above 1920x1080) are untested
