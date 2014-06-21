A Thrift-Based HiveServer2 Connection and Execution Example in PHP
==================================================================

This illustratory example, given the lack of a *sasl*-enabled ``TSaslTransport`` in Apache Thrift's PHP libraries, will only work with HiveServer2s that have their ``hive.server2.authentication`` configuration set to ``NOSASL``.

Program sources are in ``HS2Client.php``

Dependencies (Thrift libs, and Hive I/F) are under ``thrift/`` and ``hive/``

Namespace (php) modified thrift spec for Hive I/F is under ``hive-spec``

Running
=======

```bash
git clone git@github.com:QwertyManiac/hive-hs2-php-thrift
cd hive-hs2-php-thrift
php HS2Client.php
```
