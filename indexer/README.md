# indexer

Indexer 1.2 was coded, developed, and envisioned by Tim Erickson with loads of modifications by Angelo Ashmore, studiopd.com.

###### README:
This is a simple index file that can be thrown in any folder and will manage and display all files and folders in the directory it resides, and can even navigate to sub directories and display them in the same way.
It has several specific features, including the ability to navigate folders, show photos, play music, and even movies.

###### MORE:
Because php currently has no way of auto-detecting the size of the movie file, the script uses a nifty work-around to show the video properly.
Simply create a txt file for the movie found in the folder with the width and height information in html.
Movie info naming schema is `*.dim`, where `*` represents the name of the movie file.
For example: Video.mov.dim would be the describing info for the Video.mov file.
Since its html, you can add any tags for the movie that you may want, such as the autoplay tag.
For example: `height="430" width="612" autoplay="false"`
Quick tip, you should add about 16 pixels to the height of each video, to allow for the player controls to show up.

###### NEW IN 1.2:
Can now navigate through subdirectories with ease!
New design, cleaner
Code should be neater and faster
EXIF Reading removed in this version to increase speed (and it wasn't all that popular or implemented in a pretty fashion)
Probably something I missed

###### KNOWN BUG LIST:
Don't remember, probably most of the stuff from 1.1

![preview](https://raw.githubusercontent.com/neutyp/indexer/master/indexer/indexer.png)
