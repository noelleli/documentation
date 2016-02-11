## a result processing script

import os
import sys
import pandas as pd

from pyspark import SparkContext, SparkConf
from pyspark.sql import SQLContext


if __name__ == "__main__":
    appName = "read1"
    conf = SparkConf().setMaster("spark://Noelles-MBP:7077").setAppName(appName)
    sc = SparkContext(conf = conf)
    sqlContext = SQLContext(sc) 
    df = sqlContext.read.json("/Users/noelleli/contextly/stage1_4_results/*")
    pddf = df.toPandas()
    pddf_filter = pddf[(pddf['datetime'] >= '2015-12-01') & (pddf['datetime'] <= '2015-12-31')] 
    pddf_filter.to_csv("Dec_PubDate_Authors_Postid.csv", encoding='utf-8')
