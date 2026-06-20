1- Psql exec without binary

```bash
docker compose exec database psql app app
```
2- Symfony Ai 

```sh
symfony composer req symfony/ai-bundle symfony/ai-agent symfony/ai-open-ai-platform
```

3- Coffre-fort par environement

```sh
symfony console secrets:set OPENAI_API_KEY
```
3-a : Pour relire une chaine secrete depuis le coffre-fort
```bash
symfony console secrets:reveal OPENAI_API_KEY
```

4- Consommer des messages dans le terminal

```bash
symfony console messenger:consume async -vv
```
4-a: Lancer des workers en arriere-plan

```bash
symfony run -d --watch=config,src,templates,vendor/composer/installed.json symfony console messenger:consume async -vv
```
4-b: Lister les workers en arriere-plan
```bash
symfony server:status
```
4-c: Stopper un Worker
```bash
kill idPID_RESULTAT_DU_STATUS
```

5- Prendre des décisions avec un workflow
5-a : Creer un workflos
```bash
symfony composer req workflow
```
5b: générez une représentation visuelle au format Mermaid 
```bash
symfony console workflow:dump comment --dump-format=mermaid
```
