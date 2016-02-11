setwd("/Users/noelleli/contextly/")
dat <- read.csv("clicked.csv")
datviews <- read.csv("cookies.csv")

library(data.table)
dtviews <- data.table(datviews)
byauthor <- dtviews[, list(viewercounts = length(unique(cookie_id)), postviews = length(postid)), by = list(author)]