setwd("/Users/noelleli/contextly/")
df <- read.csv("pubdate_cat.csv")
dfauthor <- df[,-c(2,7)]
dfauthors <- dfauthor[!duplicated(dfauthor),]
dfauthors$pub_date <- as.Date(dfauthors$pub_date, "%m/%d/%y")
dfauthors$pub_year <- as.factor(format(dfauthors$pub_date, "%Y"))
dfauthors$pub_week <- as.factor(format(dfauthors$pub_date, "%m"))
library(data.table)
dtauthors <- data.table(dfauthors)
byauthors <- dtauthors[,list(postcounts = length(unique(postid)), postviews = sum(viewcounts)), by = list(author, pub_year, pub_week)]
write.csv(file = "byauthor.csv", x = byauthors)
dfcategory <- df[, -c(2,3)]