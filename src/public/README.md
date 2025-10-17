# Ahorcado — Proyecto completo

Este paquete contiene una implementacioin funcional del juego del **Ahorcado** en PHP con sesiones, siguiendo el diagrama propuesto y con **coidigo documentado con PHPDoc**.

```
WordProvider  ──►  Game  ◄── Storage
       │               │
       └──────────────►│
                       │
                   Renderer
```

## Requisitos
- PHP 8.1+
- Extensioin de sesiones habilitada

## Estructura
```
ahorcado/
├─ public/
│  └─ index.php
├─ src/
│  ├─ Game.php
│  ├─ WordProvider.php
│  ├─ Storage.php
│  └─ Renderer.php
├─ data/
│  └─ words.txt
├─ images/
│  ├─ mock.drawio
│  └─ mock.png (placeholder)
├─ tests/
│  ├─ GameTest.php
│  └─ WordProviderTest.php
├─ composer.json
├─ .gitignore
└─ README.md
```

## Uso rapido
1. Instala dependencias (opcional, solo para autoload y tests):
   ```bash
   composer install
   ```
2. Inicia el servidor embebido de PHP:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Abre en el navegador: http://localhost:8000

## GitHub
```bash
git init
git add .
git commit -m "feat: ahorcado v1 (implementacioin completa con PHPDoc)"
git branch -M main
git remote add origin https://github.com/tu-usuario/ahorcado.git
git push -u origin main

git checkout -b v1
git push -u origin v1
```

## Notas
- `images/mock.drawio` es un mock basico de la UI. Puedes editarlo con draw.io / diagrams.net.
- `images/mock.png` es un **placeholder** exportado vacio, sustituyelo por tu export real si lo deseas.
- El coidigo esta documentado con **PHPDoc** para facilitar mantenimiento y pruebas.

¡Disfruta! 🎯
