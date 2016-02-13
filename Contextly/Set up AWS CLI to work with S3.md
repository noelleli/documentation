# set up AWS CLI to work with S3

### 1. check python version on ec2 instance or your local machine (most likely it is 2.7)
```cmd
$ python --version
```

### 2. install pip
```cmd
$ sudo python27 get-pip.py
```

### 3. install aws cli
```cmd
$ pip install awscli
```
- upgrade awscli
```cmd
$ pip install --upgrade awscli
```

### 4. get user credential and key from aws aim console in order to use AWS CLI
- go [here](https://console.aws.amazon.com/iam/)
- create key pair for yourself if there isn't one already;
- check if your profile is in developer group (everybody is in developer group, which happens to have full access to everything!?!?) 

### 5. add your creditials to aws cli configuration file
```cmd
$ aws configure ##fill in each of the following when prompted w/ json as output
AWS Access Key ID [None]:
AWS Secret Access Key [None]:
Default region name [None]:
Default output format [None]:
```

### 6. read [this](http://docs.aws.amazon.com/cli/latest/userguide/using-s3-commands.html) for how to use aws cli

