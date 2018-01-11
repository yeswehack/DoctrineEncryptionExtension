<?php

$executionStart = microtime(true);
$memoryStart = memory_get_usage(true);

$em = include 'em.php';

$repository = $em->getRepository('Yaml\ArticleSimpleKey');
$article = $repository->findOneByTitle('Article 1');
if (!$article) {
    $article = new Yaml\ArticleSimpleKey();
    $article->setTitle('Article 1');
    $article->setEncrypted('Encrypted');

    $em->persist($article);
    $em->flush();
}

$repository = $em->getRepository('Yaml\ArticleProtectedKey');
$article = $repository->findOneByTitle('Article 1');
if (!$article) {
    $article = new Yaml\ArticleProtectedKey();
    $article->setTitle('Article 1');
    $article->setEncrypted('Encrypted');

    $em->persist($article);
    $em->flush();
}

$ms = round(microtime(true) - $executionStart, 4) * 1000;
$mem = round((memory_get_usage(true) - $memoryStart) / 1000000, 2);
echo "Execution took: {$ms} ms, memory consumed: {$mem} Mb";
