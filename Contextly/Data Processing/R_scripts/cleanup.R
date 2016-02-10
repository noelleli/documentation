
df <- read.csv("Dec_PubDate_Authors_Postid.csv")
df$pub_date <- as.Date(df$pub_date, "%m/%d/%y")
df$pub_year <- as.factor(format(df$pub_date, "%Y"))
df$pub_week <- as.factor(format(df$pub_date, "%m"))
library(data.table)
dtauthors <- data.table(df)
byauthors <- dtauthors[,list(postcounts = length(unique(post_id)), postviews = sum(viewcounts)), by = list(author, pub_year, pub_week)]
write.csv(file = "byauthor_dec.csv", x = byauthors)
