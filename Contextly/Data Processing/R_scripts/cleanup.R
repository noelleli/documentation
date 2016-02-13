## read the file in. Make sure the file name is correct.
df <- read.csv("almost_final.csv")
## change the published date column into date format.
df$pub_date <- as.Date(df$pub_date, format = "%Y-%m-%d")
## get the year and month of the date
df$pub_year <- as.factor(format(df$pub_date, "%Y"))
df$pub_week <- as.factor(format(df$pub_date, "%m"))

## load the library to do the count unique and sum by operations
library(data.table)
dtauthors <- data.table(df)
byauthors <- dtauthors[,list(postcounts = length(unique(post_id)), postviews = sum(viewcounts)), by = list(author, pub_year, pub_week)]

## export the file.
write.csv(file = "byauthor.csv", x = byauthors)
