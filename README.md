# Dockerisation d'un forum php

Projet LP de __Morphée LEGUAY__ & __kevin AUBRY__
Configuration du projet hors IUT, sans proxy. Testé sur Ubuntu 16.04 xenial

* docker-compose up
* PhPMyAdmin : localhost:8001 ; login : user ; pwd : mdp. La base de donnée se  crée et s'importe automatiquement
* forum : localhost/8001/forum_culinaire/web ; login : admin ; pwd : admin

*difficultés rencontrées* :
* importation automatique de la BDD
* ajout d'un my.cnf pour MySQL afin d'accepter certaines requêtes en GROUP BY