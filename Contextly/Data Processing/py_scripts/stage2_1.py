## a test script

import os
import sys

from pyspark import SparkContext, SparkConf
from pyspark.sql import SQLContext
from pyspark.sql import functions as F
import pandas as pd


if __name__ == "__main__":
    appName = "testApp"
    conf = SparkConf().setMaster("spark://Noelles-MBP:7077").setAppName(appName)
    sc = SparkContext(conf = conf)
    sqlContext = SQLContext(sc)
    path = "/Users/noelleli/contextly/new/"
    df = sqlContext.read.json(path)           
    dfview = df.where(df['data_type'] == "MODULE_VIEW")
    postviews = dfview.select("payload.post_id", "payload.time_stamp", "payload.cookie_id", "payload.author").withColumnRenamed("post_id", "postid")
    dffinal = postviews.withColumn('datetime', F.from_unixtime(postviews['time_stamp'], format = "yyyy-MM-dd"))
    dffinal.write.mode("overwrite").json("/Users/noelleli/contextly/processed_results_cookies/")
