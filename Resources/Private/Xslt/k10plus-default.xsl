<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:mets="http://www.loc.gov/METS/"
                xmlns:mods="http://www.loc.gov/mods/v3"
                xmlns:slub="http://slub-dresden.de/"
                xmlns:foaf="http://xmlns.com/foaf/0.1/"
                xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                xmlns:person="http://www.w3.org/ns/person#"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:k="http://www.kitodo.org"
                xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-7.xsd http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd">

    <xsl:output indent="yes" method="xml"/>

    <xsl:param name="record_state"/>
    <xsl:param name="agent_name"/>
    <xsl:param name="document_type"/>
    <xsl:param name="process_number"/>

    <xsl:template match="/">
        <xsl:comment>
            Transformed by k10plus2mods stylesheet version 1.0
        </xsl:comment>

        <data>
            <slub:info>
                <slub:documentType>
                    <xsl:value-of select="$document_type"/>
                </slub:documentType>
                <slub:processNumber>
                    <xsl:value-of select="$process_number"/>
                </slub:processNumber>
                <xsl:apply-templates select="data/submitter"/>
                <xsl:apply-templates select="data/submitter/notice"/>
            </slub:info>

            <mods:mods version="3.7">
                <xsl:apply-templates select="mods:mods/mods:titleInfo/mods:title"/>
                <xsl:apply-templates select="mods:mods/mods:identifier[@type='isbn']"/>
                <xsl:apply-templates select="mods:mods/mods:name"/>
                <xsl:apply-templates select="mods:mods/mods:originInfo[@eventType='publication']/mods:dateIssued"/>
            </mods:mods>
        </data>
    </xsl:template>

    <xsl:template match="mods:title">
        <mods:titleInfo lang="" usage="primary">
            <mods:title>
                <xsl:value-of select="." />
            </mods:title>
        </mods:titleInfo>
    </xsl:template>

    <xsl:template match="mods:identifier">
        <mods:identifier type="isbn">
                <xsl:value-of select="." />
        </mods:identifier>
    </xsl:template>

    <xsl:template match="mods:name">
        <xsl:for-each select=".">
            <xsl:if test="@type='personal'">
                <mods:name type="personal">
                    <mods:namePart type="family">
                        <xsl:value-of select="mods:namePart" />
                    </mods:namePart>
                    <xsl:if test="./mods:role/mods:roleTerm[@type='code' and @authority='marcrelator']">
                        <mods:role>
                            <mods:roleTerm type="code" authority="marcrelator"><xsl:value-of select="mods:role/mods:roleTerm[@type='code' and @authority='marcrelator']" /></mods:roleTerm>
                        </mods:role>
                    </xsl:if>
                </mods:name>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="mods:dateIssued">
        <mods:originInfo eventType="publication">
            <mods:dateIssued encoding="iso8601">
                <xsl:value-of select="." />
            </mods:dateIssued>
        </mods:originInfo>
    </xsl:template>

</xsl:stylesheet>