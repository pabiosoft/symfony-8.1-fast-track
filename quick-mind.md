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
