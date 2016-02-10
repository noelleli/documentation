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
    dfview = df.where(df['payload.clicked'] == True)
    postviews = dfview.select("payload.post_id_from", "payload.author_to", "payload.post_id_to", "payload.author_from", "payload.time_stamp", "payload.cookie_id")
    dffinal = postviews.withColumn('datetime', F.from_unixtime(postviews['time_stamp'], format = "yyyy-MM-dd"))
    dffinal.write.mode("overwrite").json("/Users/noelleli/contextly/processed_results_clicked/")
