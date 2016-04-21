module_gmap
===========
GMap module is an extension for plugin I developped for one of my own sites in order to show its members location.

Tested with version 3.8.0 of PHPFox.

##Requirements##
- Version 3.8.x of PHPFox
- Apache or NGINX server with SQL, PHP

##Features##
- Auto installation of the module + SQL tables
- Add a "gmap" component
- Add a block to look for specific people and research per country
- Add a hook to update someone's location automatically as they update their profile info
- Show a Googlemap with an icon on the map for all your members.
- Upon clicking on the icon, will show a quick profile of the user, with link to their profile.

##How to install##
- Copy/paste the "gmap" folder in the "module" folder of your PHPFox installation
- In the administration panel of PHPFox, go to Extensions > Module management
- Find gmap in the list, and click to the install icon.
- Note: In this version of the project, the users will have to reenter their address for it to appear on GoogleMap

##TODO##
- Initialize the location of the existing users.
- More GUI settings

##Thanks##
- Yohann CERDAN for his googlemap script.
