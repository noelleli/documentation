#!/usr/bin/python
#
# Copyright 2013 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

"""Setup script for the Google Ads Python Client Library."""

__author__ = 'Mark Saniscalchi'

import os
import re
import sys
from setuptools import setup

PACKAGES = ['googleads']

DEPENDENCIES = ['httplib2', 'oauth2client', 'suds-jurko', 'pysocks', 'pytz',
                'PyYAML']

CLASSIFIERS = [
    'Intended Audience :: Developers',
    'License :: OSI Approved :: Apache Software License',
    'Programming Language :: Python :: 2.7',
    'Programming Language :: Python :: 3.4'
]


def GetVersion():
  """Gets the version from googleads/common.py.

  We can't import this directly because new users would get ImportErrors on our
  third party dependencies.

  Returns:
    The version of the library.
  """
  with open(os.path.join('googleads', 'common.py')) as versions_file:
    source = versions_file.read()
  return re.search('\\nVERSION = \'(.*?)\'', source).group(1)


long_description = """
===========================================
The googleads Python Client Libraries
===========================================

The googleads Python Client Libraries support the following products:

* AdWords API
* DoubleClick for Advertisers API
* DoubleClick for Publishers API

You can find more information about the Google Ads Python Client Libraries
`here <https://github.com/googleads/googleads-python-lib>`_.

Supported Python Versions
=========================

This library is supported for Python 2 and 3, for versions 2.7+ and 3.4+
respectively. It is recommended that Python 2 users use python 2.7.9+ to take
advantage of the SSL Certificate Validation feature that is not included in
earlier versions.

Installation
============

You have two options for installing the Ads Python Client Libraries:

* Install with a tool such as pip::

  $ sudo pip install googleads

* Install manually after downloading and extracting the tarball::

  $ sudo python setup.py install

Examples
========

If you would like to obtain example code for any of the included
client libraries, you can find it on our
`downloads page <https://github.com/googleads/googleads-python-lib/releases>`_.

Contact Us
==========

Do you have an issue using the googleads Client Libraries? Or perhaps some
feedback for how we can improve them? Feel free to let us know on our
`issue tracker <https://github.com/googleads/googleads-python-lib/issues>`_.
"""

extra_params = {}
if sys.version_info[0] == 3:
  extra_params['use_2to3'] = True

setup(name='googleads',
      version=GetVersion(),
      description='Google Ads Python Client Library',
      author='Mark Saniscalchi',
      author_email='msaniscalchi@google.com',
      url='https://github.com/googleads/googleads-python-lib',
      license='Apache License 2.0',
      long_description=long_description,
      packages=PACKAGES,
      platforms='any',
      keywords='adwords adxbuyer dfp dfa google',
      classifiers=CLASSIFIERS,
      install_requires=DEPENDENCIES,
      **extra_params)
