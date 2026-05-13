<?php
// logger.php
// Inclou aquest fitxer a totes les pàgines per registrar els accessos a MongoDB.
// Exemple d'ús: require_once 'logger.php';

function logAccess(): void {
    try {
        require_once __DIR__ . '/vendor/autoload.php';

        $mongoUri = getenv('MONGO_URI') ?: 'mongodb://root:example@mongo:27017';
        $client   = new MongoDB\Client($mongoUri);

        // Base de dades: gi3p_logs | Col·lecció: access_logs
        $collection = $client->gi3p_logs->access_logs;

        // --- Dades de la petició ---

        // URL visitada
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
             . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
             . ($_SERVER['REQUEST_URI'] ?? '/');

        // Mètode HTTP
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';

        // Usuari autenticat (adapta si tens sessió o autenticació pròpia)
        // Si la teva app no té login, sempre serà null
        $user = $_SESSION['usuari'] ?? $_SESSION['username'] ?? null;

        // IP del client (considera proxies)
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';
        // Si hi ha múltiples IPs (proxy chain), agafem la primera
        $ip = explode(',', $ip)[0];
        $ip = trim($ip);

        // User-Agent del navegador
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // Inserir document a MongoDB
        $collection->insertOne([
            'url'       => $url,
            'method'    => $method,
            'user'      => $user,          // null si no autenticat
            'timestamp' => new MongoDB\BSON\UTCDateTime(),  // data/hora UTC
            'browser'   => $userAgent,
            'ip'        => $ip,
        ]);

    } catch (Exception $e) {
        // No bloquejem l'aplicació si MongoDB falla
        // Podeu activar el log en mode debug:
        // error_log('MongoDB logger error: ' . $e->getMessage());
    }
}

logAccess();