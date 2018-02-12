drop table if exists t_user;
drop table if exists t_categorie;
drop table if exists t_topic;
drop table if exists t_post;


create table t_user (
    usr_id integer not null primary key auto_increment,
    usr_name varchar(200) not null,
    usr_password varchar(88) not null,
    usr_picture varchar(2000),
    usr_salt varchar(23) not null,
    usr_role varchar(50) not null,    
    usr_online boolean not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table t_categorie (
    cat_id integer not null primary key auto_increment,
    cat_name varchar(50) not null,
    cat_description varchar(150) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table t_topic (
    top_id integer not null primary key auto_increment,
    top_title varchar(100) not null,
    top_content varchar(10000) not null,
    top_date datetime not null,
    usr_id integer not null,
    cat_id integer not null,
    constraint fk_usr_idT foreign key(usr_id) references t_user(usr_id),
    constraint fk_cat_idT foreign key(cat_id) references t_categorie(cat_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table t_post (
    post_id integer not null primary key auto_increment,
    post_content varchar(10000) not null,
    post_date datetime not null,
    usr_id integer not null,
    top_id integer not null,
    constraint fk_usr_idP foreign key(usr_id) references t_user(usr_id),
    constraint fk_top_idP foreign key(top_id) references t_topic(top_id)
) engine=innodb character set utf8 collate utf8_unicode_ci;



insert into t_user(usr_name, usr_password, usr_salt, usr_role, usr_online) values
('kevin', '$2y$13$e/uW8jJPS7Gro1bgfIObGeUnlzvpkvf/vtr2rZ25pNAN54WUW7OGC', '4749773cd10e7ac55801bac', 'ROLE_USER', 0);
insert into t_user(usr_name, usr_password, usr_salt, usr_role, usr_online) values
('admin', '$2y$13$kmDnLSJcd/Q.6FuM0LL5OuiaMQGk0H2G5oS1ZmRDQLW5jmoOW07sS', '4749773cd10e7ac55801bac', 'ROLE_ADMIN', 0);
insert into t_user(usr_id, usr_name, usr_password, usr_salt, usr_role, usr_online) values
(404, 'utilisateur supprimé', '$2y$13$e/uW8jJPS7Gro1bgfIObGeUnlzvpkvf/vtr2rZ25pNAN54WUW7OGC', '4749773cd10e7ac55801bac', 'ROLE_ADMIN', 0);

insert into t_categorie(cat_name, cat_description) values
('Aperitifs', 'Ici on parle des aperitifs.');
insert into t_categorie(cat_name, cat_description) values
('Entrees', 'Ici on parle des entrees.');
insert into t_categorie(cat_name, cat_description) values
('Plat', 'Ici on parle des plats de resistance.');
insert into t_categorie(cat_name, cat_description) values
('dessert', 'Ici on parle des desserts en tout genre.');


insert into t_topic(top_title, top_content, top_date, usr_id, cat_id) values
('La biere', 'Brune, ambree ou blanche', '2017-01-01', 1, 1);
insert into t_topic(top_title, top_content, top_date, usr_id, cat_id) values
('Les gateaux aperitifs', 'Les biscuits aperitifs sont sales', '2017-01-01', 1, 2);
insert into t_topic(top_title, top_content, top_date, usr_id, cat_id) values
('Les crepes salees', ' crepe au fromage ?', '2017-01-01', 1, 3);
insert into t_topic(top_title, top_content, top_date, usr_id, cat_id) values
('Les gauffres', 'Avec ou sans oeufs', '2017-01-01', 1, 4);

INSERT INTO `forum_culinaire`.`t_post` (`post_id`, `post_content`, `post_date`, `usr_id`, `top_id`) VALUES (NULL, ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nisi leo, feugiat vel imperdiet ac, tristique vel eros. Pellentesque ultrices nisi in ultricies dapibus. Maecenas non dolor a eros finibus egestas non sit amet justo. Pellentesque sit amet pretium orci. Aliquam dapibus ligula ut est elementum fermentum. Aliquam vel ipsum laoreet, condimentum magna nec, sollicitudin nunc. Curabitur quis risus aliquet eros efficitur placerat vitae eu arcu. Mauris eu metus nisi. Nulla et nisl fermentum, rutrum leo non, dignissim ante.

Sed consectetur odio magna, rutrum tempus turpis aliquam eu. Nunc pretium lorem lectus, et congue nisi aliquet at. Sed malesuada enim eros, et aliquet metus malesuada ut. Duis et turpis leo. Proin id dolor cursus, laoreet ligula at, molestie purus. Maecenas lobortis, mauris non fringilla condimentum, justo ex laoreet nibh, ut blandit justo tortor at neque. Ut turpis justo, luctus in tortor vitae, commodo sollicitudin nunc. Cras sodales rhoncus nulla eu suscipit. Etiam non leo at lacus lacinia dapibus quis eget eros. Nunc id sollicitudin nibh. Nullam blandit posuere metus, nec suscipit sem sollicitudin porttitor. Proin libero urna, mollis sit amet sapien vel, varius gravida metus. Integer lobortis, neque non varius molestie, justo est euismod turpis, sed tempor sem magna in ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus laoreet magna luctus posuere congue. Nulla in lacus dictum, maximus ex eget, suscipit lorem. ', '2018-01-17 00:00:00', '1', '1');

INSERT INTO `forum_culinaire`.`t_post` (`post_id`, `post_content`, `post_date`, `usr_id`, `top_id`) VALUES (NULL, ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nisi leo, feugiat vel imperdiet ac, tristique vel eros. Pellentesque ultrices nisi in ultricies dapibus. Maecenas non dolor a eros finibus egestas non sit amet justo. Pellentesque sit amet pretium orci. Aliquam dapibus ligula ut est elementum fermentum. Aliquam vel ipsum laoreet, condimentum magna nec, sollicitudin nunc. Curabitur quis risus aliquet eros efficitur placerat vitae eu arcu. Mauris eu metus nisi. Nulla et nisl fermentum, rutrum leo non, dignissim ante.

Sed consectetur odio magna, rutrum tempus turpis aliquam eu. Nunc pretium lorem lectus, et congue nisi aliquet at. Sed malesuada enim eros, et aliquet metus malesuada ut. Duis et turpis leo. Proin id dolor cursus, laoreet ligula at, molestie purus. Maecenas lobortis, mauris non fringilla condimentum, justo ex laoreet nibh, ut blandit justo tortor at neque. Ut turpis justo, luctus in tortor vitae, commodo sollicitudin nunc. Cras sodales rhoncus nulla eu suscipit. Etiam non leo at lacus lacinia dapibus quis eget eros. Nunc id sollicitudin nibh. Nullam blandit posuere metus, nec suscipit sem sollicitudin porttitor. Proin libero urna, mollis sit amet sapien vel, varius gravida metus. Integer lobortis, neque non varius molestie, justo est euismod turpis, sed tempor sem magna in ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus laoreet magna luctus posuere congue. Nulla in lacus dictum, maximus ex eget, suscipit lorem. ', '2018-01-17 00:00:00', '2', '2');

INSERT INTO `forum_culinaire`.`t_post` (`post_id`, `post_content`, `post_date`, `usr_id`, `top_id`) VALUES (NULL, ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nisi leo, feugiat vel imperdiet ac, tristique vel eros. Pellentesque ultrices nisi in ultricies dapibus. Maecenas non dolor a eros finibus egestas non sit amet justo. Pellentesque sit amet pretium orci. Aliquam dapibus ligula ut est elementum fermentum. Aliquam vel ipsum laoreet, condimentum magna nec, sollicitudin nunc. Curabitur quis risus aliquet eros efficitur placerat vitae eu arcu. Mauris eu metus nisi. Nulla et nisl fermentum, rutrum leo non, dignissim ante.

Sed consectetur odio magna, rutrum tempus turpis aliquam eu. Nunc pretium lorem lectus, et congue nisi aliquet at. Sed malesuada enim eros, et aliquet metus malesuada ut. Duis et turpis leo. Proin id dolor cursus, laoreet ligula at, molestie purus. Maecenas lobortis, mauris non fringilla condimentum, justo ex laoreet nibh, ut blandit justo tortor at neque. Ut turpis justo, luctus in tortor vitae, commodo sollicitudin nunc. Cras sodales rhoncus nulla eu suscipit. Etiam non leo at lacus lacinia dapibus quis eget eros. Nunc id sollicitudin nibh. Nullam blandit posuere metus, nec suscipit sem sollicitudin porttitor. Proin libero urna, mollis sit amet sapien vel, varius gravida metus. Integer lobortis, neque non varius molestie, justo est euismod turpis, sed tempor sem magna in ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus laoreet magna luctus posuere congue. Nulla in lacus dictum, maximus ex eget, suscipit lorem. ', '2018-01-17 00:00:00', '1', '3');

INSERT INTO `forum_culinaire`.`t_post` (`post_id`, `post_content`, `post_date`, `usr_id`, `top_id`) VALUES (NULL, ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nisi leo, feugiat vel imperdiet ac, tristique vel eros. Pellentesque ultrices nisi in ultricies dapibus. Maecenas non dolor a eros finibus egestas non sit amet justo. Pellentesque sit amet pretium orci. Aliquam dapibus ligula ut est elementum fermentum. Aliquam vel ipsum laoreet, condimentum magna nec, sollicitudin nunc. Curabitur quis risus aliquet eros efficitur placerat vitae eu arcu. Mauris eu metus nisi. Nulla et nisl fermentum, rutrum leo non, dignissim ante.

Sed consectetur odio magna, rutrum tempus turpis aliquam eu. Nunc pretium lorem lectus, et congue nisi aliquet at. Sed malesuada enim eros, et aliquet metus malesuada ut. Duis et turpis leo. Proin id dolor cursus, laoreet ligula at, molestie purus. Maecenas lobortis, mauris non fringilla condimentum, justo ex laoreet nibh, ut blandit justo tortor at neque. Ut turpis justo, luctus in tortor vitae, commodo sollicitudin nunc. Cras sodales rhoncus nulla eu suscipit. Etiam non leo at lacus lacinia dapibus quis eget eros. Nunc id sollicitudin nibh. Nullam blandit posuere metus, nec suscipit sem sollicitudin porttitor. Proin libero urna, mollis sit amet sapien vel, varius gravida metus. Integer lobortis, neque non varius molestie, justo est euismod turpis, sed tempor sem magna in ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus laoreet magna luctus posuere congue. Nulla in lacus dictum, maximus ex eget, suscipit lorem. ', '2018-01-17 00:00:00', '1', '4');

