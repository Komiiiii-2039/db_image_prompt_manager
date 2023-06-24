create table image(
        ImageID int auto_increment primary key,
        Data text,
        Seed text,
        CFG float,
        Steps int,
        Width int,
        Height int,
        ModelID int,
        vaeID int,
        SamplerID int
);
create table prompt(
        PromptID int auto_increment primary key,
        Text text 
);

create table sampler(
        SamplerID int auto_increment primary key,
        Name varchar(255)
);

create table VAE(
        vaeID int auto_increment primary key,
        Name varchar(255)
);

create table model(
        ModelID int auto_increment primary key,
        Name varchar(255)
);

create table generate(
        ImageID int,
        PromptID int primary key,
        Type varchar(10)
);
