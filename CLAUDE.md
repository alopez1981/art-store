# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# Contexto del Desarrollador y Reglas de Trabajo

<developer_profile>

- Empresa: Hoyvoy.
- Perfil: Especialista Backend transicionando a Fullstack.
- Idiomas de comunicación: Español o Catalán.
  </developer_profile>

<backend_php_rules>

- Paradigmas obligatorios: Arquitectura Hexagonal, DDD (Domain-Driven Design), CQRS y principios SOLID.
- Regla estricta: Mantén una separación absoluta entre las capas de Dominio, Aplicación e Infraestructura.
- Restricción: Aunque el proyecto base tenga una estructura MVC de Laravel, para el código nuevo o refactorizaciones,
  NUNCA propongas soluciones acopladas al framework en la capa de dominio a menos que se pida explícitamente.
  </backend_php_rules>

<frontend_vue_rules>

- Stack moderno: Usa siempre Vue.js con Composition API y `<script setup>`.
- Pedagogía: El usuario es experto en backend. Cuando expliques reactividad, estado o ciclo de vida en Vue, utiliza
  analogías de backend (ej. inyección de dependencias, controladores, eventos de dominio) para facilitar la comprensión.
  </frontend_vue_rules>

<database_sql_rules>

- Enfoque: Rendimiento bruto y estricta integridad referencial.
- Al generar esquemas o migraciones, prioriza el uso correcto de índices, claves foráneas y prevención de cuellos de
  botella.
  </database_sql_rules>

<response_format>

1. Entrega siempre el código limpio y listo para implementar.
2. Añade siempre una sección breve de "Justificación Técnica" explicando el "porqué" de las decisiones arquitectónicas
   tomadas.
   </response_format>

---

## Project Overview

Art Store is a full-stack e-commerce application for selling artwork, built with Laravel 13, Vue 3, and Inertia.js. It
uses Stripe for payments.

**Tech Stack:** PHP 8.3, Laravel 13, Vue 3, Inertia.js 3, Tailwind CSS 4, Vite 8, SQLite, Stripe

## Commands

```bash
# Initial setup (installs dependencies, creates .env, runs migrations, builds assets)
composer run setup

# Development (starts Laravel server, queue worker, log viewer, and Vite concurrently)
composer run dev

# Run tests
composer run test

# Single test file
php artisan test tests/Feature/ExampleTest.php

# Single test method
php artisan test --filter=test_method_name

# Build frontend assets
npm run build
