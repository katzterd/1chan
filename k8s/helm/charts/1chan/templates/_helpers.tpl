{{/*
Registry override
*/}}
{{- define "ImageRegistry" -}}
{{- if .Values.registry }}
{{- .Values.registry }}
{{- else }}
{{- .Values.defaultRegistry }}
{{- end }}
{{- end }}

{{/*
Default secrets name override
*/}}
{{- define "SecretsName" -}}
{{- if .Values.secretsName }}
{{- .Values.secretsName }}
{{- else }}
{{- .Values.defaultSecretsName }}
{{- end }}
{{- end }}

{{/*
Image pull secrets
*/}}
{{- define "ImagePullSecret" -}}
{{- if .Values.imagePullSecretName -}}
imagePullSecrets:
- name: {{ .Values.imagePullSecretName }}
{{- end }}
{{- end }}

{{/*
Default storageClass name override
*/}}
{{- define "StorageClassName" -}}
{{- if .Values.storageClass.name }}
{{- .Values.storageClass.name }}
{{- else }}
{{- .Values.storageClass.defaultName }}
{{- end }}
{{- end }}

{{/*
mariadb space request override
*/}}
{{- define "DbSpace" -}}
{{- if .Values.dbSpace }}
{{- .Values.dbSpace }}
{{- else }}
{{- .Values.storageClass.pods.db.defaultRequestSpace }}
{{- end }}
{{- end }}

{{/*
www space request override
*/}}
{{- define "WWWSpace" -}}
{{- if .Values.wwwSpace }}
{{- .Values.wwwSpace }}
{{- else }}
{{- .Values.storageClass.pods.www.defaultRequestSpace }}
{{- end }}
{{- end }}

{{/*
redis space request override
*/}}
{{- define "RedisSpace" -}}
{{- if .Values.redisSpace }}
{{- .Values.redisSpace }}
{{- else }}
{{- .Values.storageClass.pods.redis.defaultRequestSpace }}
{{- end }}
{{- end }}
