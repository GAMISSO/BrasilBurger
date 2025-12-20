# Image SDK
FROM mcr.microsoft.com/dotnet/sdk:9.0 AS build
WORKDIR /app
COPY . .
# Diagnostics: print SDK info to help debug restore failures on CI (Render)
RUN dotnet --info

# Ensure NuGet cache dir exists and is writable (some CI environments have permission issues)
RUN mkdir -p /root/.nuget/packages && chmod -R 777 /root/.nuget
ENV NUGET_PACKAGES=/root/.nuget/packages

# Restore with detailed verbosity and no cache to get clearer errors on failure
RUN dotnet restore --no-cache --verbosity detailed

RUN dotnet publish -c Release -o out

# Image runtime
FROM mcr.microsoft.com/dotnet/aspnet:9.0
WORKDIR /app
COPY --from=build /app/out .
EXPOSE 8080
ENV ASPNETCORE_URLS=http://+:8080
ENTRYPOINT ["dotnet", "cshrap.dll"]