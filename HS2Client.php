<?php

    require_once 'thrift/Thrift/ClassLoader/ThriftClassLoader.php';
    use Thrift\ClassLoader\ThriftClassLoader;
    $loader = new ThriftClassLoader();
    $loader->registerNamespace('Thrift', realpath(dirname(__FILE__)).'/thrift');
    $loader->registerDefinition('org\apache\hive\service\cli\thrift', realpath(dirname(__FILE__)).'/hive');
    $loader->register();

    use Thrift\Type\TType;
    use Thrift\Protocol\TBinaryProtocol;
    use Thrift\Transport\TSocket;
    use Thrift\Transport\TBufferedTransport;

    use org\apache\hive\service\cli\thrift\TCLIServiceClient;
    use org\apache\hive\service\cli\thrift\TOpenSessionReq;
    use org\apache\hive\service\cli\thrift\TCloseSessionReq;
    use org\apache\hive\service\cli\thrift\TExecuteStatementReq;
    use org\apache\hive\service\cli\thrift\TFetchResultsReq;

    try {
        $socket = new TSocket('localhost', '10000');
        $socket->setSendTimeout(30 * 1000);
        $socket->setRecvTimeout(30 * 1000);
        $transport = new TBufferedTransport($socket);
        $protocol = new TBinaryProtocol($transport);

        // Create a HS2 client
        $client = new TCLIServiceClient($protocol);

        // Open up the connection
        $transport->open();
        echo "Opened transport\n";

        $sorq = new TOpenSessionReq();
        $sorq->username = "hive";
        $sorq->password = "password";

        $rorq = $client->OpenSession($sorq);
        echo "Opened session\n";

        $sessionHandle = $rorq->sessionHandle;

        $execreq = new TExecuteStatementReq();
        $execreq->sessionHandle = $sessionHandle;
        $execreq->statement = "SHOW TABLES";

        $execresp = $client->ExecuteStatement($execreq);
        echo "Executed: ".$execreq->statement."\n";

        $opHandle = $execresp->operationHandle;

        $fetchreq = new TFetchResultsReq();
        $fetchreq->operationHandle = $opHandle;
        $fetchreq->maxRows = 10000;

        $fetchres = $client->FetchResults($fetchreq);
        echo "Results:\n";
        foreach ($fetchres->results->rows as &$row) {
            foreach ($row->colVals as $cols) {
                echo $cols->stringVal->value;
            }
            echo "\n";
        }

        $scrq = new TCloseSessionReq();
        $scrq->sessionHandle = $sessionHandle;
        $rcrq = $client->CloseSession($scrq);
        echo "Closed session\n";

        // And finally, we close connection
        $transport->close();
        echo "Closed transport\n";
    } catch (TException $tx) {
        echo "ThriftException: ".$tx->getMessage()."\r\n";
    }
?>
