## a result processing script

import os
import sys
import pandas as pd

from pyspark import SparkContext, SparkConf
from pyspark.sql import SQLContext


if __name__ == "__main__":
    appName = "process_stage_2"
    conf = SparkConf().setMaster("spark://Noelles-MBP:7077").setAppName(appName)
    sc = SparkContext(conf = conf)
    sqlContext = SQLContext(sc) 
    df = sqlContext.read.json("/Users/noelleli/contextly/processed_results_clicked/*")
    pddf = df.toPandas()
    pddf.to_csv("/Users/noelleli/contextly/clicked.csv", encoding='utf-8')
