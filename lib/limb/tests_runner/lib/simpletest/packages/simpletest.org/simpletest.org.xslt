<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <!-- $Id: simpletest.org.xslt,v 1.1 2005/08/20 20:08:18 pp11 Exp $ -->

    <xsl:output method="html" indent="yes" />
    <xsl:preserve-space elements="*"/>
    
    <xsl:template match="/">
        <html>
    <xsl:call-template name="menu"/>
    <xsl:call-template name="masthead"/>
    <div class="content">
	<xsl:apply-templates select="//content/node()"/>
    </div>
    <xsl:call-template name="copyright"/>
        </html>
    </xsl:template>
    
    <xsl:template name="menu">
        <div class="menu_back">
            <div class="menu">
                <xsl:variable name="map" select="document('map_simpletest.org.xml')/page"/>
                <h2>
                    <xsl:call-template name="menu_item">
                        <xsl:with-param name="here" select="/page/@here"/>
                        <xsl:with-param name="map" select="$map"/>
                    </xsl:call-template>
                </h2>
                <xsl:call-template name="menu_layer">
                    <xsl:with-param name="here" select="/page/@here"/>
                    <xsl:with-param name="map" select="$map"/>
                </xsl:call-template>
            </div>
        </div>
    </xsl:template>
    
    <xsl:template name="find_here">
        <xsl:param name="here"/>
        <xsl:param name="map"/>
        <xsl:choose>
            <xsl:when test="$map/@title = $here">
                <xsl:value-of select="$here"/>
            </xsl:when>
            <xsl:when test="count($map/page) = 0"></xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="$map/page">
                    <xsl:call-template name="find_here">
                        <xsl:with-param name="here" select="$here"/>
                        <xsl:with-param name="map" select="."/>
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="menu_item">
        <xsl:param name="here"/>
        <xsl:param name="map"/>
        <xsl:choose>
            <xsl:when test="$map/@title = $here">
                <span class="chosen"><xsl:value-of select="$map/@title"/></span>
            </xsl:when>
            <xsl:otherwise>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="$map/@file"/></xsl:attribute>
                    <xsl:value-of select="$map/@title"/>
                </a>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="menu_layer">
        <xsl:param name="here"/>
        <xsl:param name="map"/>
        <xsl:if test="$map/page">
            <ul>
                <xsl:for-each select="$map/page">
                    <li>
                        <xsl:call-template name="show_menu_entry">
                            <xsl:with-param name="here" select="$here"/>
                            <xsl:with-param name="map" select="."/>
                        </xsl:call-template>
                    </li>
                </xsl:for-each>
            </ul>
        </xsl:if>
    </xsl:template>
    
    <xsl:template name="show_menu_entry">
        <xsl:param name="here"/>
        <xsl:param name="map"/>
        <xsl:call-template name="menu_item">
            <xsl:with-param name="here" select="$here"/>
            <xsl:with-param name="map" select="$map"/>
        </xsl:call-template>
        <xsl:variable name="is_in">
            <xsl:call-template name="find_here">
                <xsl:with-param name="here" select="$here"/>
                <xsl:with-param name="map" select="$map"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$is_in = $here">
                <xsl:call-template name="menu_layer">
                    <xsl:with-param name="here" select="$here"/>
                    <xsl:with-param name="map" select="$map"/>
                </xsl:call-template>
            </xsl:when>
        </xsl:choose>
    </xsl:template>
     
    <xsl:template name="masthead">
        <h1><xsl:value-of select="//page/@title"/></h1>
    </xsl:template>
    
    <xsl:template name="copyright">
        <div class="copyright">
            Copyright<br/>Marcus Baker, Jason Sweat, Perrick Penet 2004
        </div>
    </xsl:template>
    
    <xsl:template match="php">
        <pre>
            <xsl:call-template name="preserve_strong">
                <xsl:with-param name="raw" select="."/>
            </xsl:call-template>
        </pre>
    </xsl:template>
    
    <xsl:template match="code">
        <span class="new_code">
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    
    <xsl:template match="sh">
        <pre class="shell">
            <xsl:call-template name="preserve_strong">
                <xsl:with-param name="raw" select="."/>
            </xsl:call-template>
        </pre>
    </xsl:template>
    
    <xsl:template match="file">
        <pre class="file">
            <xsl:apply-templates/>
        </pre>
    </xsl:template>
    
    <xsl:template match="section">
        <p>
            <a class="target">
                <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
                <h2><xsl:value-of select="@title"/></h2>
            </a>
        </p>
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="introduction">
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="news">
    </xsl:template>
    
    <xsl:template match="a">
        <xsl:copy>
            <xsl:for-each select="@class|@name|@href">
                <xsl:attribute name="{local-name(.)}"><xsl:value-of select="."/></xsl:attribute>
            </xsl:for-each>
            <xsl:for-each select="@local">
                <xsl:attribute name="href">
                    <xsl:value-of select="."/><xsl:text>.html</xsl:text>
                </xsl:attribute>
            </xsl:for-each>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>
    
    <xsl:template match="*">
        <xsl:copy>
            <xsl:for-each select="@*">
                <xsl:attribute name="{local-name(.)}"><xsl:value-of select="."/></xsl:attribute>
            </xsl:for-each>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>
    
    <xsl:template match="*" mode="links">
        <li><xsl:apply-templates/></li>
    </xsl:template>
    
    <xsl:template name="preserve_strong">
        <xsl:param name="raw"/>
        <xsl:choose>
            <xsl:when test="contains($raw, '&lt;strong&gt;') and contains($raw, '&lt;/strong&gt;')">
                <xsl:value-of select="substring-before($raw, '&lt;strong&gt;')"/>
                <strong>
                    <xsl:value-of select="substring-before(substring-after($raw, '&lt;strong&gt;'), '&lt;/strong&gt;')"/>
                </strong>
                <xsl:call-template name="preserve_strong">
                    <xsl:with-param name="raw" select="substring-after($raw, '&lt;/strong&gt;')"/>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="$raw"/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>