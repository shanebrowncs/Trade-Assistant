# Trade-Assistant
Trade Assistant is a web application designed to help users analyze and compare prices for Steam inventories and CSGOLounge trades

A live version of this repository can be found at http://trade-assist.cu.cc/

![Trade Assist Layout](https://i.imgur.com/QLoT6Z5.png)

## Libraries

Library Modifications are marked with the header "TRADE-ASSIST EDIT"

- [Sorttable (Modified Version)](http://www.kryogenix.org/code/browser/sorttable/) - Credit to Stuart Langridge

## Installation

- Clone project to the path you would like to configure the server in. It is recommended that you don't run the setup on a live server, but rather set it up locally and then deploy.
- Run setup.php and fill out the database information you would like to use, the script will create a database and table to store all the CS:GO items that exist. As well it will create a file called "settings.ini" that stores the database information you entered, so that the Trade Assistant can access the item database later. By default the server folder is denying access to the end user, so your database information will be safe.
- Setup a cronjob for server/grabAllItems.php, this script is resumable so don't worry about limiting the execution time. It will resume from the market page it left off on. The more you run this cronjob the better as it ensures users have the most up to date information. Worst case scenario the user can use the "Manual Pricing" option in Settings to directly fetch all the prices(at the cost of speed).
- (OPTIONAL) In the event you change any database information you can update that info for the Trade Assistant by manually modifying server/settings.ini.
